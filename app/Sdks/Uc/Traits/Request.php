<?php

namespace App\Sdks\Uc\Traits;


use App\Common\Tools\CustomException;

trait Request
{

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return mixed
     * @throws CustomException
     * 携带认证请求
     */
    public function authRequest($url, $param = [], $method = 'GET', $header = [], $option = []){


        $reqParam =  [
            'header' => [
                'username'  => $this->getAccountName(),
                'password'  => $this->getAccountPassword(),
                'token'     => $this->getToken()
            ]
        ];

        if(!empty($param)){
            $reqParam['body'] = $param;
        }

        if(!empty($this->getTargetAccountName())){
            $reqParam['header']['target'] = $this->getTargetAccountName();
        }


        $header = array_merge([
            'Content-Type: application/json;',
        ], $header);
        return $this->publicRequest($url, json_encode($reqParam), $method, $header, $option);
    }


    /**
     * @param $url
     * @param $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return mixed
     * @throws CustomException
     * 携带认证表单请求
     */
    public function formDataRequest($url,$param, $method = 'POST', $header = [], $option = []){
        $reqParam =  [
            'username'  => $this->getAccountName(),
            'password'  => $this->getAccountPassword(),
            'token'     => $this->getToken()
        ];

        if(!empty($this->getTargetAccountName())){
            $reqParam['target'] = $this->getTargetAccountName();
        }

        if(!empty($param)){
            $reqParam = array_merge($reqParam,$param);
        }
//dd(json_encode($reqParam));
        $header = array_merge([
            'Content-Type: multipart/form-data;',
            'username:' . $this->getAccountName(),
            'password:' . $this->getAccountPassword(),
            'token:' . $this->getToken(),
            'target:' . $this->getTargetAccountName(),
        ], $header);
        return $this->publicRequest($url, json_encode($reqParam), $method, $header, $option);

    }


    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return mixed
     * @throws CustomException
     * 公共请求
     */
    private function publicRequest($url, $param = [], $method = 'GET', $header = [], $option = []){
        $ret = $this->curlRequest($url, $param, $method, $header, $option);

        $result = json_decode($ret, true);

        if(empty($result) || !isset($result['header']['status']) || $result['header']['status'] != 0){
            // 错误提示
            $errorMessage = $result['message'] ?? '公共请求错误';

            throw new CustomException([
                'code' => 'PUBLIC_REQUEST_ERROR',
                'message' => $errorMessage,
                'log' => true,
                'data' => [
                    'url' => $url,
                    'header' => $header,
                    'param' => $param,
                    'result' => $result,
                ],
            ]);
        }
        return $result['body'];
    }

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return bool|string
     * @throws CustomException
     * CURL请求
     */
    private function curlRequest($url, $param = [], $method = 'GET', $header = [], $option = []){
        $ch = $this->buildCurl($url, $param, $method, $header, $option);

        $result = curl_exec($ch);

        //$info = curl_getinfo($ch);

        $errno = curl_errno($ch);

        if(!!$errno){
            throw new CustomException([
                'code' => 'CURL_REQUEST_ERROR',
                'message' => 'CURL请求错误',
                'log' => true,
                'data' => [
                    'url' => $url,
                    'header' => $header,
                    'param' => $param,
                    'result' => $result,
                    'error' => $errno,
                ],
            ]);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return false|resource
     * 构建curl
     */
    private function buildCurl($url, $param = [], $method = 'GET', $header = [], $option = []){
        $method = strtoupper($method);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $header = array_merge($header, ['Connection: close']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if(stripos($url, 'https://') === 0){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $timeout = $option['timeout'] ?? 30;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        return $ch;
    }

}
