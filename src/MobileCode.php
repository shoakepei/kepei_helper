<?php
/*
* @Author: your name
* @Date: 2020-07-13 16:12:15
* @LastEditTime: 2020-07-23 10:28:05
* @LastEditors: Please set LastEditors
* @Description: In User Settings Edit
* @FilePath: \thinkphp6_Backend\app\model\AuditAuditor.php
*/ 
declare (strict_types = 1);

namespace kepei_helper;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;


use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20190711\SmsClient;
use TencentCloud\Sms\V20190711\Models\SendSmsRequest;


/**
 * 短信
 * @mixin think\Model
 */
class MobileCode
{
    
    /**
     * @description: 发送短信验证码  阿里
     * @param array   data
     * @return: 
     */
    public function aliSend($data)
    {
        AlibabaCloud::accessKeyClient(ALI_KEY,ALI_VALUE)
        ->regionId('cn-hangzhou')
        ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->scheme('https') // https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => $data,
            ])
            ->request();
            // dump($result);
            // dump($result->toArray());
            // if($result->toArray()['Code'] == 'ok'){
                return 1;
            // }else{
            //     return 0;
            // }


        } catch (ClientException $e) {
            return $e->getErrorMessage();
            // echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            return $e->getErrorMessage();
            // echo $e->getErrorMessage() . PHP_EOL;
        }
    }

    public function tencentSend($data)
    {
        try{
            $cred = new Credential(TENCENT_ID, TENCENT_KEY);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");
            
            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "", $clientProfile);

            $req = new SendSmsRequest();
            
            $req->fromJsonString(json_encode($data));

            $resp = $client->SendSms($req);

            $res = json_decode($resp->toJsonString());
            if($res){
                return 1;
            }else{
                dump($res);
                return 0;
            }
            
        }catch(TencentCloudSDKException $e) {
            echo $e;
            return 0;
        }
    }
}
