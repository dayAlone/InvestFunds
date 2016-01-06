<?
class InvestFunds
{
    private $funds = array();
    private $urls = array(
        'funds' => array(
            'search' => 'http://pif.investfunds.ru/ajax/funds.php?limit=100000&q=',
            'list'   => 'http://pif.investfunds.ru/ajax/funds_json.php',
            'detail' => 'http://pif.investfunds.ru/graph/main_page_pif_data.php'
        )
    );
    private function fetchFundsList() {
        $this->funds = json_decode(file_get_contents($this->urls['funds']['list']), true);
    }
    public function getFundsList() {
        if (count($this->funds) === 0) $this->fetchFundsList();
        return $this->funds;
    }
    public function getAllFundCosts($fund) {
        $url  = $this->urls['funds']['detail'] . '?id=' . urlencode($fund);
        $raw = json_decode(file_get_contents($url));
        $data = $raw->series[1]->data;
        $list = array();
        for ($i=0; $i < count($data); $i++) {
            $item = $data[$i];
            $list[date('d.m.Y', $item[0]/1000)] = $item[1];
        }
        return $list;
    }
    public function getFundCost($fund, $date = false) {
        $time = strtotime($date);
        $day  = date('w', $time);
        if (in_array($day, array(0, 6))) {
            $time = strtotime(date('d.m.Y', $time) . ' ' . ($day == 0 ? '+1 day' : '-1 day'));
        }

        $list = $this->getAllFundCosts($fund);
        $keys = array_keys($list);
        $result = $list[date('d.m.Y', $time)];

        if (!$date || !$result) return $list[$keys[count($keys) - 1]];
        else return $result;
    }
    public function searchFundByName($name) {
        $url = $this->urls['funds']['search'] . urlencode($name);
        $result = array();
        $raw = file_get_contents($url);
        $raw = strip_tags(str_replace('<span', '|<span', $raw));
        $raw = preg_split("/\n/", $raw);
        for ($i=0; $i < count($raw); $i++) {
            $item = $raw[$i];
            if (strlen($item) > 0) {
                $item = preg_split("/\|/", $item);
                $result[$item[2]] = str_replace('-', ' — ', $item[0]);
            }
        }
        if (count($result) === 1) {
            if (intval(array_keys($result)[0]) > 0) return array_keys($result)[0];
            else return array_values($result)[0];
        }
        return $result;
    }

}
?>
