<?php


namespace Qietugou\Bookmark;


use Qietugou\Bookmark\Exceptions\JsonException;
use Qietugou\Bookmark\Exceptions\RuntimeException;

class Bookmark
{
    const BOOK_TYPE = 'url';
    /**
     * @var $path
     */
    protected $path;

    /**
     * @var
     */
    protected $concise = true;

    /**
     * 存放所有的数据
     * @var array
     */
    protected $bookmarkResult = [];

    /**
     * 存放 浏览器数据 bar
     * @var array
     */
    protected $bookmarkBar = [];

    /**
     * 存放 浏览器数据 other
     * @var array
     */
    protected $bookmarkOther = [];

    /**
     * 存放 移动端浏览器数据 other
     * @var array
     */
    protected $bookmarkSynced = [];

    /**
     * Bookmark constructor.
     * @param $path
     * @param bool $concise
     */
    public function __construct($path, $concise = true)
    {
        $this->path = $path;
        $this->concise = $concise;
    }

    /**
     * @return $this
     * @throws JsonException
     * @throws RuntimeException
     */
    public function getBookmarks(): self
    {
        $bookmark = file_get_contents($this->path);
        if (!$bookmark) {
            throw new RuntimeException('No such file or directory');
        }

        $bookmark = json_decode($bookmark, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException('json error');
        }

        if (array_key_exists('roots', $bookmark)) {
            $this->bookmarkResult = $bookmark['roots'];
            $this->bookmarkBar = $this->bookmarkResult['bookmark_bar'] ?: [];
            $this->bookmarkOther = $this->bookmarkResult['other'] ?: [];
        }

        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    private function conciseResult(array $data): array
    {
        if (empty($data) || !$this->concise) {
            return $data;
        }
        $result = [];
        $result['children'] = $this->formatConcise($data['children'], $result);
        $result['name'] = $data['name'];
        return $result;
    }

    /**
     * @param $data
     * @param $result
     * @return mixed
     */
    private function formatConcise($data, $result)
    {
        foreach ($data as $key => $val) {
            if (array_key_exists('children', $val) && $val['children']) {
                $result[$key]['children'][] = $this->formatConcise($val['children'], []);
            } else {
                $result[$key]['name'] = $val['name'];
                $result[$key]['type'] = $val['type'];
                if ($val['type'] === self::BOOK_TYPE) {
                    $result[$key]['url'] = $val['url'];
                }
            }
        }
        return $result;
    }

    /**
     * @return array
     */
    public function toBarResult(): array
    {
        return $this->conciseResult($this->bookmarkBar);
    }

    /**
     * @return array
     */
    public function toOtherResult(): array
    {
        return $this->conciseResult($this->bookmarkOther);
    }

    /**
     * @return array
     */
    public function toSyncedResult(): array
    {
        return $this->conciseResult($this->bookmarkSynced);
    }

    /**
     * @return array
     */
    public function toResult(): array
    {
        return [
            'bar' => $this->toBarResult(),
            'other' => $this->toOtherResult(),
            'synced' => $this->toSyncedResult(),
        ];
    }
}