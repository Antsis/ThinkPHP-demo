<?php
    session_start();
    header('content-type:image/png');
    
    $bgColors = [233,236,239];
    $length = 4;
    $imageW = 80;
    $imageH = 24;
    $fontS = 22;
    $fontA = [-20, 20];
    $image = imagecreatetruecolor($imageW, $imageH);
    $bgColor = imagecolorallocate($image, $bgColors[0], $bgColors[1], $bgColors[2]);
    imagefill($image, 0, 0, $bgColor);

    //描绘内容

    $data = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';

    $captcha ='';
    //生成验证码
    for($i=0;$i<$length;$i++){
        $fontColor = imagecolorallocate($image, rand(0, 120), rand(0,120), rand(0, 120));
        $fontContext = substr($data, rand(0, strlen($data)-1), 1);

        
        $x= $i* $fontS + rand($fontS/3, $imageW/$length-$fontS);
        $y= rand($imageH*0.89, $imageH*0.97);

        $captcha .= $fontContext;

        imagettftext($image, $fontS, rand($fontA[0], $fontA[1]), $x, $y, $fontColor, 'C:\Users\wcz\Documents\GitHub\thinkphp-dome\vendor\topthink\think-captcha\assets\zhttfs\1.ttf', $fontContext);

    }
    $_SESSION['captcha'] = strtolower($captcha);

    //描绘点
    for($i=0;$i<($imageH*$imageW/5);$i++){
        $pointColor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
        imagesetpixel($image, rand(1, $imageW), rand(1, $imageH), $pointColor);
    }
    //描绘线
    for($i=0;$i<3;$i++){
        $lineColre = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
        imageline($image, rand(0, $imageW), rand(0, $imageH), rand(0, $imageW), rand(0, $imageH), $lineColre);
    }
    //输出图像
    imagepng($image);

    //销毁图像
    imagedestroy($image);