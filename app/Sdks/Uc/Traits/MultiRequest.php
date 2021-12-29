<?php

namespace App\Sdks\Uc\Traits;

use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\ErrorLogService;

trait MultiRequest
{
    /**
     * @var int
     * 并发请求块大小
     */
    protected $multiChunkSize = 3;


    /**
     * @param $chunkSize
     * @return bool
     * 设置并发请求块大小
     */
    public function setMultiChunkSize($chunkSize){
        $this->multiChunkSize = $chunkSize;
        return true;
    }

    /**
     * @return int
     * 获取并发请求块大小
     */
    public function getMultiChunkSize(){
        return $this->multiChunkSize;
    }

    /**
     * @param $curlOptions
     * @return array
     * 并发请求
     */
    public function multiPublicRequest($curlOptions){
        $chunkSize = $this->getMultiChunkSize();
        $chunks = array_chunk($curlOptions, $chunkSize);

Functions::consoleDump("uc multi public chuck size({$chunkSize})");
$i = 1;
        $response = [];
        foreach($chunks as $chunk){
            $response = array_merge($response, $this->multiCurlRequest($chunk));
Functions::consoleDump("chunk block({$i})");
$i++;
        }

        $succ = $err = [];
        foreach($response as $k => $v){

            if(!empty($v['error'])){
                $errorLogService = new ErrorLogService();
                $errorLogService->create(
                    'MULTI_CURL_REQUEST_ERROR',
                    '并发CURL请求错误',
                    [
                        'url' => $v['url'],
                        'error' => $v['error'],
                        'info' => $v['info'],
                        'req' => $v['req'],
                    ],
                    ExceptionTypeEnum::CUSTOM
                );

                continue;
            }

            $result = json_decode($v['result'], true);
            if(!isset($result['header']['status']) || $result['header']['status'] != 0){
                // 错误提示
                $errorMessage = $result['msg'] ?? '并发请求错误';

                $errorLogService = new ErrorLogService();
                $errorLogService->create(
                    'MULTI_REQUEST_ERROR',
                    $errorMessage,
                    [
                        'url' => $v['url'],
                        'error' => $v['error'],
                        'info' => $v['info'],
                        'req' => $v['req'],
                        'result' => $result['header'],
                    ],
                    ExceptionTypeEnum::CUSTOM
                );
            }

            if(isset($v['req']['param'])){
                $v['req']['param'] = json_decode($v['req']['param'],true);
            }

            $succ[] = [
                'data' => $result,
                'req' => $v['req'],
            ];
        }

        return $succ;
    }

    /**
     * @param $curlOptions
     * @return array
     * 并发curl请求
     */
    private function multiCurlRequest($curlOptions){
        $mh = curl_multi_init();
        $chs = $reqs = [];
        foreach($curlOptions as $i => $curlOption){


            // 默认值
            $url = $curlOption['url'];
            $param = json_encode($curlOption['param']) ?? '';
            $method = $curlOption['method'] ?? 'GET';
            $header = $curlOption['header'] ?? [];
            $option = $curlOption['option'] ?? [];
            // 构造句柄
            $ch = $this->buildCurl($url, $param, $method, $header, $option);

            curl_multi_add_handle($mh, $ch);
Functions::consoleDump($ch);

            $chs[strval($ch)] = $ch;
            $reqs[strval($ch)] = [
                'url' => $url,
                'param' => $param,
                'method' => $method,
                'header' => $header,
                'option' => $option,
            ];
        }

        $res = [];
        do{
            if(($status = curl_multi_exec($mh, $active)) != CURLM_CALL_MULTI_PERFORM){
                if ($status != CURLM_OK) { break; } //如果没有准备就绪，就再次调用curl_multi_exec
                while ($done = curl_multi_info_read($mh)) {
                    $info = curl_getinfo($done["handle"]);
                    $error = curl_error($done["handle"]);
                    $result = curl_multi_getcontent($done["handle"]);
                    $req = $reqs[strval($done["handle"])];
                    $rtn = compact('info', 'error', 'result', 'url', 'req');

                    $res[] = $rtn;
                    curl_multi_remove_handle($mh, $done['handle']);
                    curl_close($done['handle']);

                    // 如果仍然有未处理完毕的句柄，那么就select
                    if ($active > 0) {
                        // 阻塞
                        curl_multi_select($mh, 1);
                    }
                }
            }
        }while($active > 0);

        curl_multi_close($mh);

        return $res;
    }



    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @return mixed
     * 并发获取
     */
    public function multiGet($url, array $params = [],$method = 'POST'){
        $curlOptions = [];

        foreach($params as $param){
            // username 跟 target 一致会提示不合法
            if(isset($param['header']['target']) && $this->getAccountName() == $param['header']['target']){
                unset($param['header']['target']);
            }

            $reqParam =  [
                'header' => array_merge([
                    'username'  => $this->getAccountName(),
                    'password'  => $this->getAccountPassword(),
                    'token'     => $this->getToken()
                ],$param['header'])
            ];

            if(!empty($param['body'])){
                $reqParam['body'] = $param['body'];
            }


            $curlOptions[] = [
                'url' => $url,
                'param' => $reqParam,
                'method' => $method,
                'header' => [
                    'Content-Type: application/json; charset=utf-8',
                ]
            ];
        }
        return $this->multiPublicRequest($curlOptions);
    }
}
