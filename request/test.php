<?php

$a = 'POST http://www.meituan.com/order/feedback/207361297 HTTP/1.1
Host: www.meituan.com
Connection: keep-alive
Content-Length: 111
Origin: http://www.meituan.com
X-Requested-With: XMLHttpRequest
User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36
Content-Type: application/x-www-form-urlencoded; charset=UTF-8
Accept: */*
Referer: http://www.meituan.com/rates/list/torate
Accept-Encoding: gzip,deflate,sdch
Accept-Language: zh-CN,zh;q=0.8
Cookie: SID=pikklmdg5o0s6c8ejq0snskeh6; ci=20; PHPSESSID=uuhn6e2vol1ra40s8rik3avsj4; rvd=6592406%2C8997357%2C4548176%2C8320767%2C9207011; abt=1374647007.0%7CACE; rus=1; lun=gbogh; u=11746649; n=gbogh; lt=aDSQD1d2fhQEeWahpmN1gJ-aTBP0Fccm; lsu=gbogh; __utma=1.856471171.1373512023.1374553092.1374647011.6; __utmb=1.6.9.1374647089044; __utmc=1; __utmz=1.1373966770.4.3.utmcsr=tuan.baidu.com|utmccn=tuan.baidu.com|utmcmd=nav|utmctr=baidutuan_mp^^_^^288d5729df0ea8fb8134276806cc02ee|utmcct=pic; __utmv=1.|1=city=guangzhou=1; __t=1374647085749.2.1374647089638.Anone; vipnotice_11746649=%7B%22status%22%3Atrue%2C%22growthValue%22%3A2115%2C%22showLevel%22%3A2%2C%22noticeType%22%3A0%2C%22noticeValue%22%3A0%2C%22cityid%22%3A20%7D; uuid=09d68156c2a973622af1.1373512019.1.0.1

score=3&subtype1=2&subscore1=4&subtype2=3&subscore2=3&subtype3=5&subscore3=3&wantmore=1&comment=&oldfeedbackid=';
$fp = stream_socket_client("tcp://www.meituan.com:80", $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, $a);
    $str = '';
    while (!feof($fp)) {
        $str .=fgets($fp, 1024);
    }
    list($header, $body) = preg_split("/\R\R/", $str, 2);
    var_dump($body);
    fclose($fp);
    echo gzdecode($body); 
}
function gzdecode($data) { 
  $len = strlen($data); 
  if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) { 
   return null;  // Not GZIP format (See RFC 1952) 
  } 
  $method = ord(substr($data,2,1));  // Compression method 
  $flags  = ord(substr($data,3,1));  // Flags 
  if ($flags & 31 != $flags) { 
   // Reserved bits are set -- NOT ALLOWED by RFC 1952 
   return null; 
  } 
  // NOTE: $mtime may be negative (PHP integer limitations) 
  $mtime = unpack("V", substr($data,4,4)); 
  $mtime = $mtime[1]; 
  $xfl  = substr($data,8,1); 
  $os    = substr($data,8,1); 
  $headerlen = 10; 
  $extralen  = 0; 
  $extra    = ""; 
  if ($flags & 4) { 
   // 2-byte length prefixed EXTRA data in header 
   if ($len - $headerlen - 2 < 8) { 
     return false;    // Invalid format 
   } 
   $extralen = unpack("v",substr($data,8,2)); 
   $extralen = $extralen[1]; 
   if ($len - $headerlen - 2 - $extralen < 8) { 
     return false;    // Invalid format 
   } 
   $extra = substr($data,10,$extralen); 
   $headerlen += 2 + $extralen; 
  }

  $filenamelen = 0; 
  $filename = ""; 
  if ($flags & 8) { 
   // C-style string file NAME data in header 
   if ($len - $headerlen - 1 < 8) { 
     return false;    // Invalid format 
   } 
   $filenamelen = strpos(substr($data,8+$extralen),chr(0)); 
   if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) { 
     return false;    // Invalid format 
   } 
   $filename = substr($data,$headerlen,$filenamelen); 
   $headerlen += $filenamelen + 1; 
  }

  $commentlen = 0; 
  $comment = ""; 
  if ($flags & 16) { 
   // C-style string COMMENT data in header 
   if ($len - $headerlen - 1 < 8) { 
     return false;    // Invalid format 
   } 
   $commentlen = strpos(substr($data,8+$extralen+$filenamelen),chr(0)); 
   if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) { 
     return false;    // Invalid header format 
   } 
   $comment = substr($data,$headerlen,$commentlen); 
   $headerlen += $commentlen + 1; 
  }

  $headercrc = ""; 
  if ($flags & 1) { 
   // 2-bytes (lowest order) of CRC32 on header present 
   if ($len - $headerlen - 2 < 8) { 
     return false;    // Invalid format 
   } 
   $calccrc = crc32(substr($data,0,$headerlen)) & 0xffff; 
   $headercrc = unpack("v", substr($data,$headerlen,2)); 
   $headercrc = $headercrc[1]; 
   if ($headercrc != $calccrc) { 
     return false;    // Bad header CRC 
   } 
   $headerlen += 2; 
  }

  // GZIP FOOTER - These be negative due to PHP's limitations 
  $datacrc = unpack("V",substr($data,-8,4)); 
  $datacrc = $datacrc[1]; 
  $isize = unpack("V",substr($data,-4)); 
  $isize = $isize[1];

  // Perform the decompression: 
  $bodylen = $len-$headerlen-8; 
  if ($bodylen < 1) { 
   // This should never happen - IMPLEMENTATION BUG! 
   return null; 
  } 
  $body = substr($data,$headerlen,$bodylen); 
  $data = ""; 
  if ($bodylen > 0) { 
   switch ($method) { 
     case 8: 
       // Currently the only supported compression method: 
       $data = gzinflate($body); 
       break; 
     default: 
       // Unknown compression method 
       return false; 
   } 
  } else { 
   // I'm not sure if zero-byte body content is allowed. 
   // Allow it for now...  Do nothing... 
  }

  // Verifiy decompressed size and CRC32: 
  // NOTE: This may fail with large data sizes depending on how 
  //      PHP's integer limitations affect strlen() since $isize 
  //      may be negative for large sizes. 
  if ($isize != strlen($data) || crc32($data) != $datacrc) { 
   // Bad format!  Length or CRC doesn't match! 
   return false; 
  } 
  return $data; 
}
