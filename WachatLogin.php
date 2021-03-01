<?php

namespace kepei_helper;

class WachatLogin
{
    private $param ;
    private $appid =  APP_ID;
    private $secret = APP_SECRET;
    private $redirect_uri ;
    private $response_type = [1=>'code'];
    private $scope = [1=>'snsapi_base',2=>'snsapi_userinfo'];

    private $moban1 = '"template_id":"ngqIpbwh8bUfcSsECmogfXcV14J0tQlEpBO27izEYtY",
           "data":{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   },
                   "keyword1":{
                       "value":"巧克力",
                       "color":"#173177"
                   },
                   "keyword2": {
                       "value":"39.8元",
                       "color":"#173177"
                   },
                   "keyword3": {
                       "value":"2014年9月22日",
                       "color":"#173177"
                   },
                   "remark":{
                       "value":"欢迎再次购买！",
                       "color":"#173177"
                   }
           }';

    public  function __construct()
    {

    }

    private function appendParam($key,$value)
    {
        if($this->param == null){
            $this->param = '?';
        }else{
            $this->param .= '&';
        }
        $this->param .= "$key=$value";
    }


    public function getAuthorize($redirect_uri = null,$sata = null)
    {
        $this->appendParam('appid',$this->appid);
        $this->appendParam('redirect_uri',$redirect_uri);
        $this->appendParam('response_type',$this->response_type[1]);
        $this->appendParam('scope',$this->scope[2]);
        if(isset($sata)){
            $this->appendParam('sata',$sata);
        }
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize";

        return $url.$this->param.'#wechat_redirect';
    }

    /**
     * 公众号
     */
    public function getAccess_token($code)
    {
        //授权DEMO
        $this->appendParam('appid',$this->appid);
        $this->appendParam('secret',$this->secret);
        $this->appendParam('code',$code);
        $this->appendParam('grant_type','authorization_code');
        $baseUrl = "https://api.weixin.qq.com/sns/oauth2/access_token";
        // https://api.weixin.qq.com/sns/jscode2session

        $url = $baseUrl.$this->param;
        $data = $this->curl($url);

        return $data;
    }

    /**
     * 小程序
     */
    public function getAccess_token1()
    {
        //授权DEMO
        $this->appendParam('appid',$this->appid);
        $this->appendParam('secret',$this->secret);
        $this->appendParam('grant_type','client_credential');
        $baseUrl = "https://api.weixin.qq.com/cgi-bin/token";
        // https://api.weixin.qq.com/sns/jscode2session

        $url = $baseUrl.$this->param;
        $data = $this->curl($url);

        return $data;
    }

    /**
     * 小程序
     */
    public function login($code)
    {
        //授权DEMO
        $this->appendParam('appid',$this->appid);
        $this->appendParam('secret',$this->secret);
        $this->appendParam('js_code',$code);
        $this->appendParam('grant_type','authorization_code');
        $baseUrl = "https://api.weixin.qq.com/sns/jscode2session";
        // https://api.weixin.qq.com/sns/jscode2session

        $url = $baseUrl.$this->param;
        // dump($url);
        // die;
        $data = $this->curl($url);

        return $data;
    }

    public function refresh_token($refresh_token)
    {
        //授权DEMO
        $this->appendParam('appid',$this->appid);
        $this->appendParam('grant_type','refresh_token');
        $this->appendParam('refresh_token',$refresh_token);
        $baseUrl = "https://api.weixin.qq.com/sns/oauth2/refresh_token";

        $url = $baseUrl.$this->param;
        $data = $this->curl($url);

        return $data;
    }
    
    public function getUserinfo($access_token,$openid)
    {
        //授权DEMO
        $this->appendParam('access_token',$access_token);
        $this->appendParam('openid',$openid);
        $this->appendParam('lang','zh_CN');
        $baseUrl = "https://api.weixin.qq.com/sns/userinfo";

        $url = $baseUrl.$this->param;
        $data = $this->curl($url);

        

        return $data;
    }

    /**
     * get 请求
     */
    public function curl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回,而不是直接输出.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        //  这是跳过证书验证  证书 怎么弄下来还没搞清楚
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        //执行命令
        $data = curl_exec($curl);
        if($data == false){
            return curl_error($curl);
        }
        //关闭URL请求
        curl_close($curl);

        return $data;
    }

    /**
     * post 请求
     */
    public function curl_post($url,$data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回,而不是直接输出.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // post 请求
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);     // Post提交的数据包
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);     // 设置超时限制防止死循环
        curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);

        return $data;
    }

    public function push($open_id,$access_token)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $push = '   {
           "touser":"'.$open_id.'",
           '.$this->$moban.'
       }';

       $data = $this->curl_post($url,$push);
       return $data;
    }
    
}