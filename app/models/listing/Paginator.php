<?php
// Simple Paginator helper for server-side pagination
class Paginator
{
    public $page;
    public $perPage;
    public $total;
    public $lastPage;
    public $offset;

    public function __construct(int $page = 1, int $perPage = 10)
    {
        $this->page = max(1, $page);
        $this->perPage = max(1, min(100, $perPage)); // limit max per page to 100
        $this->offset = ($this->page - 1) * $this->perPage;
        $this->total = 0;
        $this->lastPage = 0;
    }

    public function paginateQuery(PDO $pdo, string $countSql, string $dataSql, array $params = [])
    {
        // count
        $stmt = $pdo->prepare($countSql);
        $stmt->execute($params);
        $this->total = (int)$stmt->fetchColumn();
        $this->lastPage = (int)max(1, ceil($this->total / $this->perPage));

        // adjust page if out of bounds
        if ($this->page > $this->lastPage) {
            $this->page = $this->lastPage;
            $this->offset = ($this->page - 1) * $this->perPage;
        }

        // data with limit
        $dataSqlWithLimit = $dataSql . " LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($dataSqlWithLimit);
        foreach ($params as $k => $v) {
            // support numeric and named parameters
            if (is_int($k)) {
                $stmt->bindValue($k + 1, $v);
            } else {
                $stmt->bindValue(':' . ltrim($k, ':'), $v);
            }
        }
        $stmt->bindValue(':limit', $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $this->offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
    }

    public function renderLinks(string $baseUrl, array $extraParams = [])
    {
        if ($this->lastPage <= 1) return '';

        $html = '<nav aria-label="pagination"><ul style="list-style:none;padding:0;display:flex;gap:6px;">';
        // previous
        $prevPage = max(1, $this->page - 1);
        $html .= '<li><a href="' . $this->buildUrl($baseUrl, $prevPage, $extraParams) . '">Prev</a></li>';

        // simple page numbers, cap to window
        $window = 5;
        $start = max(1, $this->page - floor($window / 2));
        $end = min($this->lastPage, $start + $window - 1);
        if ($end - $start + 1 < $window) {
            $start = max(1, $end - $window + 1);
        }

        for ($p = $start; $p <= $end; $p++) {
            if ($p == $this->page) {
                $html .= '<li><strong>' . $p . '</strong></li>';
            } else {
                $html .= '<li><a href="' . $this->buildUrl($baseUrl, $p, $extraParams) . '">' . $p . '</a></li>';
            }
        }

        // next
        $nextPage = min($this->lastPage, $this->page + 1);
        $html .= '<li><a href="' . $this->buildUrl($baseUrl, $nextPage, $extraParams) . '">Next</a></li>';

        $html .= '</ul></nav>';
        return $html;
    }

    private function buildUrl(string $baseUrl, int $page, array $extraParams = [])
    {
        $params = array_merge($extraParams, ['page' => $page, 'per_page' => $this->perPage]);
        $qs = http_build_query($params);
        return $baseUrl . ($qs ? ('?' . $qs) : '');
    }
}