<?php
/**
 * Strip comments from code files across the project.
 * - Creates a backup in comment_backups/<timestamp>/
 * - Skips the vendor directory and backups directory
 * - Handles: PHP, JS, CSS, HTML, SQL, Python
 *
 * Usage: php scripts/strip_comments.php
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

$root = realpath(__DIR__ . '/..');
$timestamp = date('Ymd_His');
$backupRoot = $root . "/comment_backups/$timestamp";

$excludeDirs = [
    $root . '/backend/vendor',
    $backupRoot,
    $root . '/Images',
];

$handledExts = ['php','js','css','html','htm','sql','py'];

if (!is_dir($backupRoot) && !mkdir($backupRoot, 0777, true)) {
    fwrite(STDERR, "Failed to create backup directory: $backupRoot\n");
    exit(1);
}

echo "Root: $root\nBackup: $backupRoot\n\n";

$changed = 0; $skipped = 0; $errors = 0; $total = 0;

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $fileInfo) {
    $path = $fileInfo->getPathname();
    if ($fileInfo->isDir()) {
        // Skip excluded directories
        foreach ($excludeDirs as $ex) {
            if (strpos($path, $ex) === 0) {
                $iterator->next();
            }
        }
        continue;
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    if (!in_array($ext, $handledExts, true)) { $skipped++; continue; }

    // Skip files within excluded directories
    $skip = false;
    foreach ($excludeDirs as $ex) {
        if (strpos($path, $ex) === 0) { $skip = true; break; }
    }
    if ($skip) { $skipped++; continue; }

    $total++;
    $relPath = substr($path, strlen($root));

    $content = file_get_contents($path);
    if ($content === false) { $errors++; continue; }

    try {
        switch ($ext) {
            case 'php':
                $clean = stripPhpComments($content);
                break;
            case 'html':
            case 'htm':
                $clean = stripHtmlComments($content);
                break;
            case 'css':
                $clean = stripCssComments($content);
                break;
            case 'js':
                $clean = stripJsComments($content);
                break;
            case 'sql':
                $clean = stripSqlComments($content);
                break;
            case 'py':
                $clean = stripPyComments($content);
                break;
            default:
                $clean = $content;
        }
    } catch (Throwable $e) {
        fwrite(STDERR, "Error processing $relPath: " . $e->getMessage() . "\n");
        $errors++;
        continue;
    }

    if ($clean === $content) { $skipped++; continue; }

    // Backup original
    $backupPath = $backupRoot . $relPath;
    $backupDir = dirname($backupPath);
    if (!is_dir($backupDir) && !mkdir($backupDir, 0777, true)) {
        fwrite(STDERR, "Failed to create backup dir: $backupDir\n");
        $errors++;
        continue;
    }
    if (file_put_contents($backupPath, $content) === false) {
        fwrite(STDERR, "Failed to backup $relPath\n");
        $errors++;
        continue;
    }

    if (file_put_contents($path, $clean) === false) {
        fwrite(STDERR, "Failed to write cleaned file: $relPath\n");
        $errors++;
        continue;
    }

    $changed++;
    echo "Cleaned: $relPath\n";
}

echo "\nSummary:\n";
echo "  Total candidates: $total\n";
echo "  Changed: $changed\n";
echo "  Skipped/no change: $skipped\n";
echo "  Errors: $errors\n";

echo "\nBackups saved to: $backupRoot\n";

// --- Helpers ---
function stripPhpComments(string $code): string {
    // Preserve line endings count by replacing comments with equivalent newlines
    $tokens = token_get_all($code);
    $out = '';
    foreach ($tokens as $tok) {
        if (is_array($tok)) {
            [$id, $text] = $tok;
            if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
                $out .= preg_replace('/[^\n]/', '', $text); // keep newlines only
            } else {
                $out .= $text;
            }
        } else {
            $out .= $tok;
        }
    }
    return $out;
}

function stripHtmlComments(string $html): string {
    // Remove <!-- ... -->, not including conditional comments nuances
    return preg_replace('/<!--([\s\S]*?)-->/', '', $html);
}

function stripCssComments(string $css): string {
    return preg_replace('/\/\*[\s\S]*?\*\\//', '', $css);
}

function stripJsComments(string $js): string {
    $len = strlen($js);
    $out = '';
    $i = 0;
    $inS = false; $inD = false; $inB = false; // ' " `
    $inSL = false; $inML = false;
    while ($i < $len) {
        $c = $js[$i];
        $n = $i + 1 < $len ? $js[$i+1] : '';

        if ($inSL) { // single-line comment
            if ($c === "\n") { $inSL = false; $out .= $c; }
            $i++; continue;
        }
        if ($inML) { // multi-line comment
            if ($c === '*' && $n === '/') { $inML = false; $i += 2; continue; }
            $i++; continue;
        }
        if ($inS) {
            if ($c === "\\") { $out .= $c; $i++; if ($i < $len) { $out .= $js[$i]; $i++; } continue; }
            if ($c === "'") { $inS = false; }
            $out .= $c; $i++; continue;
        }
        if ($inD) {
            if ($c === "\\") { $out .= $c; $i++; if ($i < $len) { $out .= $js[$i]; $i++; } continue; }
            if ($c === '"') { $inD = false; }
            $out .= $c; $i++; continue;
        }
        if ($inB) { // backtick template literal
            if ($c === "\\") { $out .= $c; $i++; if ($i < $len) { $out .= $js[$i]; $i++; } continue; }
            if ($c === '`') { $inB = false; }
            $out .= $c; $i++; continue;
        }

        // Not in string/comment
        if ($c === "'") { $inS = true; $out .= $c; $i++; continue; }
        if ($c === '"') { $inD = true; $out .= $c; $i++; continue; }
        if ($c === '`') { $inB = true; $out .= $c; $i++; continue; }

        if ($c === '/' && $n === '/') { $inSL = true; $i += 2; continue; }
        if ($c === '/' && $n === '*') { $inML = true; $i += 2; continue; }

        $out .= $c; $i++;
    }
    return $out;
}

function stripSqlComments(string $sql): string {
    $len = strlen($sql); $out=''; $i=0; $inS=false; $inML=false;
    while ($i < $len) {
        $c = $sql[$i]; $n = $i+1 < $len ? $sql[$i+1] : '';
        if ($inML) { if ($c==='*' && $n=== '/') { $inML=false; $i+=2; continue; } $i++; continue; }
        if ($inS) {
            if ($c === "\\") { $out.=$c; $i++; if($i<$len){$out.=$sql[$i]; $i++;} continue; }
            if ($c === "'") { $inS=false; }
            $out.=$c; $i++; continue;
        }
        if ($c === "'") { $inS=true; $out.=$c; $i++; continue; }
        if ($c==='-' && $n==='-') { // line comment
            // consume until newline
            while ($i<$len && $sql[$i] !== "\n") { $i++; }
            continue;
        }
        if ($c==='/' && $n==='*') { $inML=true; $i+=2; continue; }
        $out.=$c; $i++;
    }
    return $out;
}

function stripPyComments(string $py): string {
    $len = strlen($py); $out=''; $i=0; $inS=false; $q=''; $triple=false;
    while ($i < $len) {
        $c = $py[$i]; $n = $i+1 < $len ? $py[$i+1] : ''; $n2 = $i+2 < $len ? $py[$i+2] : '';
        if ($inS) {
            if ($c === "\\") { $out.=$c; $i++; if($i<$len){$out.=$py[$i]; $i++;} continue; }
            if ($triple) {
                if ($c === $q && $n === $q && $n2 === $q) { $out.=$c.$n.$n2; $i+=3; $inS=false; $triple=false; continue; }
                $out.=$c; $i++; continue;
            } else {
                if ($c === $q) { $out.=$c; $i++; $inS=false; continue; }
                $out.=$c; $i++; continue;
            }
        }
        // not in string
        if (($c==="'" || $c==='"')) {
            $q=$c; $triple = ($n===$c && $n2===$c); $inS=true;
            if ($triple) { $out.=$c.$n.$n2; $i+=3; } else { $out.=$c; $i++; }
            continue;
        }
        if ($c==='#') {
            // consume until newline
            while ($i<$len && $py[$i] !== "\n") { $i++; }
            continue;
        }
        $out.=$c; $i++;
    }
    return $out;
}
