<?php
namespace Image;
/**
 * Контроллер captcha.controller 
 * Создан 09.04.2012, A.Yakubouski <irokez@tut.by>
 */
define('CAPTCHA_DIR',__DIR__.'/captcha/');

include_once CAPTCHA_DIR.'gif.class.php';

class Captcha
{
    const FontSizeMin = 16;
    const FontSizeMax = 22;
    const FontFace = 'font.ttf';
    const NumFrames = 10;
    const PI = 6.2831853071795862;
    
    private function fxLake(&$Image,$NumFrames)
    {
        $NumFrames = $NumFrames <= 0 ? 12 : $NumFrames;
        $Width = imageSX($Image);
        $Height = imageSY($Image);

        $Frames = array(); $Framed = array();
        
        for($i = 0; $i < $NumFrames; $i++)
        {
            $Offset = (self::PI * $i) / $NumFrames;
            $DestImage = imageCreateTrueColor($Width, $Height);
            
            for($j = 0; $j < $Height; $j++)
            {
                    $Skew = (($Height / 16) * ($j + 25.0) * sin((($Height / 16) * ($Height - $j)) / ($j + 1) + $Offset) / $Height)/2;
                    if($j < -$Skew)
                    {
                        imagecopy($DestImage, $Image, 0, $j, 0, $j, $Width, 1);
                    }
                    else
                    {
                        imagecopy($DestImage, $Image, 0, $j, 0, $j + $Skew, $Width, 1);
                    }
            }
            ob_start();
            imageGif($DestImage);
            $Frames[$i] = ob_get_clean();
            $Framed[$i] = 10;
            imageDestroy($DestImage);
        }
        
        $anim = new GifMerge($Frames, 0, 0, 0, -10, $Framed, 2, 0, 0, 'C_MEMORY');
        
        return $anim->getAnimation();
    }
    
    
    private function Generate($Code,$Width,$Height,$TextColor,$BkColor)
    {
        list(,$rText,$gText,$bText) = preg_match('/#?(\w{2})(\w{2})(\w{2})/s', $TextColor,$rgb)?$rgb:array(0,'00','00','00');
        list(,$rBk,$gBk,$bBk) = preg_match('/#?(\w{2})(\w{2})(\w{2})/s', $BkColor,$rgb)?$rgb:array(0,'FF','FF','FF');
        
        $hImg = imagecreate($Width, $Height);
        $clrWhite = ImageColorAllocate ($hImg, hexdec($rBk), hexdec($gBk), hexdec($bBk));
        $clrBlack = ImageColorAllocate ($hImg, hexdec($rText), hexdec($gText), hexdec($bText));
        
        imagefill($hImg,0,0,$clrWhite);
        
        $x = 20;
        for ($i=0; $i<strlen($Code); $i++)
        {
                $fh = rand(self::FontSizeMin, self::FontSizeMax);
                $aFont = ImageTTFText($hImg, $fh, rand(-40, 40), $x, rand($Height/2 - $fh/2, $Height/2 + $fh/2), $clrBlack, CAPTCHA_DIR.self::FontFace, $Code[$i]);
                $x += $aFont[2] - $aFont[0] + 8;
        }
        return $this->fxLake($hImg, self::NumFrames);
    }
    public function Get($Code,$Width,$Height,$FgColor,$BkColor)
    {
        ob_end_clean();
        header("Expires: 0");
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Content-type: image/gif");
        echo $this->Generate($Code,$Width,$Height,$FgColor,$BkColor);
        exit;
    }
}

/**
 * @return \Image\Captcha
 */
function Captcha() { return new \Image\Captcha(); }