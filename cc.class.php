<?php
class CC
{
    protected $bin;
    protected $check;
    protected $jml;

    public function __construct($bin, $check = 0, $jml = 0)
    {
        $this->bin = $bin;
        $this->check = $check;
        $this->jml = $jml;
    }

    private function color($color = "default", $text)
    {
        $arrayColor = array(
            'grey'         => '1;30',
            'red'         => '1;31',
            'green'     => '1;32',
            'yellow'     => '1;33',
            'blue'         => '1;34',
            'purple'     => '1;35',
            'nevy'         => '1;36',
            'white'     => '1;0',
        );
        return "\033[" . $arrayColor[$color] . "m" . $text . "\033[0m";
    }
    public function Execute()
    {
        echo "###############################################\n";
        echo "{~} Starting generation\n";
        echo "###############################################\n";
        sleep(5);
        if ($this->check < 1) {
            for ($i = 1; $i <= $this->jml; $i++) {
                echo $this->Extrap($this->bin) . "\n";
                sleep(1);
            }
        } else {
            for ($i = 1; $i <= $this->jml; $i++) {
                $card = $this->Extrap($this->bin);
                echo $this->Check($card) . "\n";
                sleep(1);
            }
        }
    }

    protected function generateYears()
    {
        $randMonth = rand(1, 12);
        $randYears = rand(20, 25);
        $randCvv = rand(010, 800);
        $randMonth < 10 ? $randMonth = "0" . $randMonth : $randMonth = $randMonth;
        $randCvv < 100 ? $randCvv = "0" . $randCvv : $randCvv = $randCvv;
        return "|" . $randMonth . "|20" . $randYears . "|" . $randCvv;
    }
    protected function Calculate($ccnumber, $length)
    {
        $sum = 0;
        $pos = 0;
        $reversedCCnumber = strrev($ccnumber);

        while ($pos < $length - 1) {
            $odd = $reversedCCnumber[$pos] * 2;
            if ($odd > 9) {
                $odd -= 9;
            }
            $sum += $odd;

            if ($pos != ($length - 2)) {

                $sum += $reversedCCnumber[$pos + 1];
            }
            $pos += 2;
        }

        # Calculate check digit
        $checkdigit = ((floor($sum / 10) + 1) * 10 - $sum) % 10;
        $ccnumber .= $checkdigit;
        return $ccnumber;
    }
    protected function Extrap($bin)
    {
        if (preg_match_all("#x#si", $bin)) {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $ccNumber = str_split($ccNumber);
            $replace = "";
            foreach ($ccNumber as $cc => $key) {
                $replace .= str_replace("x", rand(0, 9), $key);
            }
            $Complete = $this->Calculate($replace, 16);
        } else {
            $ccNumber = $bin;
            while (strlen($ccNumber) < (16 - 1)) {
                $ccNumber .= rand(0, 9);
            }
            $Complete = $this->Calculate($ccNumber, 16);
        }
        return $Complete . $this->generateYears();
    }
    protected function Save($title, $text)
    {
        $fopen = fopen($title, "a");
        fwrite($fopen, $text);
        fclose($fopen);
    }
    protected function Check($card)
    {
        $headers = array();
        headers = {
    'authority': 'checker.visatk.com',
    'accept': 'application/json, text/javascript, */*; q=0.01',
    'accept-language': 'en-US,en;q=0.9',
    'content-type': 'application/x-www-form-urlencoded; charset=UTF-8',
    # 'cookie': '__gads=ID=3207b887e0ef18db-2256c3ac2be00097:T=1685888062:RT=1685888062:S=ALNI_Mb1Y57cSfMdAXlHRQN8rYn8XJBLwg; __gpi=UID=00000c3d0c02546a:T=1685888062:RT=1685888062:S=ALNI_MZxOkOa0JM_SZj0VcbfSbymCzba5g; PHPSESSID=npumq9cbbpngotau2a19oj3iu6',
    'origin': 'https://checker.visatk.com',
    'referer': 'https://checker.visatk.com/ccn1/',
    'sec-ch-ua': '"Not:A-Brand";v="99", "Chromium";v="112"',
    'sec-ch-ua-mobile': '?1',
    'sec-ch-ua-platform': '"Android"',
    'sec-fetch-dest': 'empty',
    'sec-fetch-mode': 'cors',
    'sec-fetch-site': 'same-origin',
    'user-agent': 'Mozilla/5.0 (Linux; Android 9; CPH1923) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36',
    'x-requested-with': 'XMLHttpRequest',
  }        curl_setopt_array($ch, $options);
        $exec = curl_exec($ch);
        $status = json_decode($exec);
        switch ($status->error) {
            case '2':
                return $card . $this->color("red", " [ DIE ]");
                break;
            case '3':
                return $card . $this->color("grey", " [ UNKNOWN ]");
                break;
            case '4':
                return $card . $this->color("yellow", " [ CC NOT VALID ]");
                break;
            case '1':
                // $this->Save("Result-".$this->bin.".list", $card."\n");
                return $card . $this->color("green", " [ LIVE ]");
                break;
        }
    }
}
