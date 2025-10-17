<?php











namespace Predis\Connection\Resource;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class Stream implements StreamInterface
{
    



    private const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    private const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    


    private $stream;

    


    private $seekable;

    


    private $readable;

    


    private $writable;

    



    public function __construct($stream)
    {
        if (!is_resource($stream)) {
            throw new InvalidArgumentException('Given stream is not a valid resource');
        }

        $this->stream = $stream;
        $metadata = stream_get_meta_data($this->stream);
        $this->seekable = $metadata['seekable'];
        $this->readable = (bool) preg_match(self::READABLE_MODES, $metadata['mode']);
        $this->writable = (bool) preg_match(self::WRITABLE_MODES, $metadata['mode']);
    }

    


    public function __toString(): string
    {
        if ($this->isSeekable()) {
            $this->seek(0);
        }

        return $this->getContents();
    }

    


    public function close(): void
    {
        if (isset($this->stream)) {
            fclose($this->stream);
        }

        $this->detach();
    }

    


    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    


    public function getSize(): ?int
    {
        if (!isset($this->stream)) {
            return null;
        }

        $stats = fstat($this->stream);
        if (is_array($stats) && isset($stats['size'])) {
            return $stats['size'];
        }

        return null;
    }

    


    public function tell(): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        $result = ftell($this->stream);

        if ($result === false) {
            throw new RuntimeException('Unable to determine stream position');
        }

        return $result;
    }

    


    public function eof(): bool
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        return feof($this->stream);
    }

    


    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    


    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isSeekable()) {
            throw new RuntimeException('Stream is not seekable');
        }

        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new RuntimeException("Unable to seek stream from offset {$offset} to whence {$whence}");
        }
    }

    


    public function rewind(): void
    {
        $this->seek(0);
    }

    


    public function isWritable(): bool
    {
        return $this->writable;
    }

    



    public function write(string $string): int
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isWritable()) {
            throw new RuntimeException('Cannot write to a non-writable stream');
        }

        $result = fwrite($this->stream, $string);

        if ($result === false) {
            throw new RuntimeException('Unable to write to stream', 1);
        }

        return $result;
    }

    


    public function isReadable(): bool
    {
        return $this->readable;
    }

    




    public function read(int $length): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }

        if ($length < -1) {
            throw new RuntimeException('Length parameter cannot be negative');
        }

        if (0 === $length) {
            return '';
        }

        if ($length === -1) {
            $string = fgets($this->stream);
        } else {
            $string = fread($this->stream, $length);
        }

        if (false === $string) {
            throw new RuntimeException('Unable to read from stream', 1);
        }

        return $string;
    }

    


    public function getContents(): string
    {
        if (!isset($this->stream)) {
            throw new RuntimeException('Stream is detached');
        }

        if (!$this->isReadable()) {
            throw new RuntimeException('Cannot read from non-readable stream');
        }

        return stream_get_contents($this->stream);
    }

    



    public function getMetadata(?string $key = null)
    {
        if (!isset($this->stream)) {
            return null;
        }

        if (!$key) {
            return stream_get_meta_data($this->stream);
        }

        $metadata = stream_get_meta_data($this->stream);

        return $metadata[$key] ?? null;
    }
}
