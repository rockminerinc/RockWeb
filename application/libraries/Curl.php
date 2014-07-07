<?php
/*
文件名: Curl.class.php
作用: Curl相关功能
*/
class Curl
{
  /*
  $submit_url 提交到的url
  $submit_vars 提交的数据 Array
  返回String
  */
  function submit($url, $data)
{
//要发送POST的字段和值
$ch = curl_init() or exit(curl_error());
//发送的浏览器信息
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux i686; pl; rv:1.8.0.3) Gecko/20060426 Firefox/1.5.0.3');
    if(is_array($data))
    {
      $params = '';
      foreach($data as $key=>$val)
      {
        if(is_array($val))
        {
          foreach($val as $key2=>$val2)
          {
            $params .= $key.'['.$key2.']='.$val2.'&';
          }
        }
        else
        {
          $params .= $key.'='.$val.'&';
        }
      }
      $params = substr($params, 0, -1);
    }
    else
    {
      return false;
    }
curl_setopt($ch, CURLOPT_POST, 1); //以POST方式提交
curl_setopt($ch, CURLOPT_POSTFIELDS, $params); //提交的数据
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置CURL,让其返回数据
$data=curl_exec($ch) or die(curl_error($ch));
//echo curl_error($ch);
curl_close($ch);
return $data ? $data : false;
}
}
?>