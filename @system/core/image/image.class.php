<?php
namespace Image;
/**
 * Description of image
 * @author Andrei Yakubouski
 */
class Image
{
    private $canvasImage;
    private $canvasColorTable;
    protected $canvasSize;
    protected $canvasAntiAliassing;

    private static $AntialiasQuality = 0;

    public function  __construct($Width=0,$Height=0,$alphaBlending=true,$antiAliassing=true)
    {
        $this->canvasColorTable = array();
        if($Width && $Height)
        {
            $this->Create($Width, $Height, $alphaBlending, $antiAliassing);
        }
    }

    public function  __destruct()
    {
        $this->Delete();
    }

    static public function Color($Color)
    {
        if(is_string($Color) && preg_match('/#([0-9abcdef]{2})([0-9abcdef]{2})([0-9abcdef]{2})([0-9abcdef]{2})?/i', $Color,$RGB))
        {
            if(isset($RGB[4])) { $RGB[4] = intval($RGB[4], 16); if($RGB[4] > 0x7f) $RGB[4] = 0x7f;}
            else {$RGB[4] = 0;}
            return array(intval($RGB[1], 16),intval($RGB[2], 16),intval($RGB[3], 16),$RGB[4]);
        }
    }

    public function RGB($Color)
    {
        if(func_num_args() == 1)
            list($R,$G,$B,$A) = $Color;
        else
            list($R,$G,$B,$A) = func_get_args();
        $rgba = ($A << 24) | ($R << 16) | ($G << 8) | $B;
        if(isset($this->canvasColorTable[$rgba])) return $rgba;

        $ColorId = imagecolorallocatealpha($this->canvasImage, $R,$G,$B,$A);
        $this->canvasColorTable[$rgba] = 1;

        return $ColorId;
    }

    public function Delete()
    {
        foreach($this->canvasColorTable as $color)
        {
            imagecolordeallocate($this->canvasImage,$color);
        }
        if($this->canvasImage)
            imagedestroy($this->canvasImage);
            
        $this->canvasColorTable = array();
        $this->canvasImage = NULL;
    }

    public function GetWidth() {return $this->canvasSize[0];}
    public function GetHeight() {return $this->canvasSize[1];}
    public function GetSize() { return $this->canvasSize; }

    public function Create($Width,$Height,$alphaBlending=true,$antiAliassing=true)
    {
        if(($this->canvasImage = imagecreatetruecolor($Width, $Height)))
        {
            if($antiAliassing && function_exists('imageantialias') ) { imageantialias($this->canvasImage, true); $this->canvasAntiAliassing = true; }
            //if($antiAliassing) { imageantialias ($this->canvasImage, true); $this->canvasAntiAliassing = true; }
            if(!$antiAliassing && $alphaBlending) imagealphablending($this->canvasImage, true);
            $this->canvasSize = array($Width,$Height);
            $this->SetFont();
        }
    }
    
    public function SetImage($Image,$Width,$Height) {
	$this->canvasSize = [$Width,$Height];
	$this->canvasImage = $Image;
	$this->SetFont();
    }
    
    public function LoadImage($FilePathName) {
	// Run a cheap check to verify that it is an image file.
	if (false === ( $this->canvasSize = getimagesize($FilePathName) ))
	    return false;

	if (false === ( $file_data = file_get_contents($FilePathName) ))
	    return false;

	if (false === ( $this->canvasImage = imagecreatefromstring($file_data) ))
	    return false;
	imagealphablending($this->canvasImage, true);
	unset($file_data);
	$this->SetFont();
	return $this;
    }

    public function SetFont($fontSize=10,$fontName='arial')
    {
        $this->canvasFontName = $fontName;
        $this->canvasFontSize = $fontSize;
    }

    public function GetTextBox($Text,$FontSize=NULL)
    {
        $rect =  imagettfbbox ( $FontSize?$FontSize:$this->canvasFontSize , 0 , $this->canvasFontName , $Text);

        $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
        $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
        $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));

        return array(
        "left"   => abs($minX),
        "top"    => abs($minY),
        "width"  => $maxX - $minX,
        "height" => $maxY - $minY,
        );
    }

    public function DrawText($Text,$X,$Y,$Color,$Angle=0,$FontSize=NULL)
    {
        imagettftext($this->canvasImage, $FontSize?$FontSize:$this->canvasFontSize, $Angle, $X,$Y, $this->RGB($Color), $this->canvasFontName, $Text);
    }

    private function drawAlphaPixel($X,$Y,$R,$G,$B,$A)
    {
        $RGB2 = imagecolorat($this->canvasImage, $X, $Y);
        $R2   = ($RGB2 >> 16) & 0xFF;
        $G2   = ($RGB2 >> 8) & 0xFF;
        $B2   = $RGB2 & 0xFF;

        $iAlpha = (100 - $A)/100;
        $A  = $A / 100;

        $Ra   = floor($R*$A+$R2*$iAlpha);
        $Ga   = floor($G*$A+$G2*$iAlpha);
        $Ba   = floor($B*$A+$B2*$iAlpha);
        imagesetpixel($this->canvasImage,$X,$Y,$this->RGB($Ra,$Ga,$Ba));
    }

    public function SetPixel($X,$Y,$Color)
    {
        list($R,$G,$B,$A) = $Color;
        $A = 100 - $A;
        if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
        if ( $G < 0 ) { $G = 0; } if ( $G > 255 )  { $G = 255; }
        if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

        $Xi   = floor($X);
        $Yi   = floor($Y);

        if ( $Xi == $X && $Yi == $Y)
        {
            if ( $A == 100 )
            {
                imagesetpixel($this->canvasImage,$X,$Y,$this->RGB($R,$G,$B,$A));
            }
            else
            {
                $this->drawAlphaPixel($X, $Y, $R, $G, $B, $A);
            }
        }
        else
        {
            $Alpha1 = (((1 - ($X - floor($X))) * (1 - ($Y - floor($Y))) * 100) / 100) * $A;
            if ( $Alpha1 > self::$AntialiasQuality )
            { $this->drawAlphaPixel($Xi,$Yi,$R,$G,$B,$Alpha1); }

            $Alpha2 = ((($X - floor($X)) * (1 - ($Y - floor($Y))) * 100) / 100) * $A;
            if ( $Alpha2 > self::$AntialiasQuality )
            { $this->drawAlphaPixel($Xi+1,$Yi,$R,$G,$B,$Alpha2); }

            $Alpha3 = (((1 - ($X - floor($X))) * ($Y - floor($Y)) * 100) / 100) * $A;
            if ( $Alpha3 > self::$AntialiasQuality )
            { $this->drawAlphaPixel($Xi,$Yi+1,$R,$G,$B,$Alpha3); }

            $Alpha4 = ((($X - floor($X)) * ($Y - floor($Y)) * 100) / 100) * $A;
            if ( $Alpha4 > self::$AntialiasQuality )
            { $this->drawAlphaPixel($Xi+1,$Yi+1,$R,$G,$B,$Alpha4); }
        }
    }

    public function Line($X1,$Y1,$X2,$Y2,$Color,$Think=1)
    {
        if($Think > 1) { imagesetthickness($this->canvasImage, $Think); }
        imageline($this->canvasImage, $X1,$Y1,$X2,$Y2,$this->RGB($Color));
        if($Think > 1) { imagesetthickness($this->canvasImage, 1); }
    }

    public function PolyLine($Points,$Color,$Think=1)
    {
        if($Think > 1)
            { imagesetthickness($this->canvasImage, $Think); }
        $numberPoints = count($Points);
        for ($i=0; $i<$numberPoints-1; $i++)
        {
            imageline($this->canvasImage, $Points[$i][0],$Points[$i][1],$Points[$i+1][0],$Points[$i+1][1],$this->RGB($Color));
        }
        if($Think > 1)
            { imagesetthickness($this->canvasImage, 1); }
    }

    private function __rgb($rgb)
    {
        return array(($rgb >> 16) & 0xFF,($rgb >> 8) & 0xFF,$rgb & 0xFF);
    }

    public function AALine($X1,$Y1,$X2,$Y2,$Color,$LineWidth=1)
    {
        $Distance = sqrt(($X2-$X1)*($X2-$X1)+($Y2-$Y1)*($Y2-$Y1));
        if ( $Distance == 0 )
            return(-1);
        $XStep = ($X2-$X1) / $Distance;
        $YStep = ($Y2-$Y1) / $Distance;

        for($i=0;$i<=$Distance;$i++)
        {
            $X = $i * $XStep + $X1;
            $Y = $i * $YStep + $Y1;

            if ($LineWidth == 1 )
                $this->SetPixel($X,$Y,$Color);
            else
            {
                $StartOffset = -($this->LineWidth/2); $EndOffset = ($this->LineWidth/2);
                for($j=$StartOffset;$j<=$EndOffset;$j++)
                    $this->SetPixel($X+$j,$Y+$j,$Color);
            }
        }
    }

    public function AAPolyline($Points,$Color)
    {
        $numberPoints = count($Points);
        for ($i=0; $i<$numberPoints-1; $i++)
        {
            $this->AALine($Points[$i][0],$Points[$i][1],$Points[$i+1][0],$Points[$i+1][1],$Color);
        }
    }

    public function AAPolygon($Points,$Color)
    {
        $numberPoints = count($Points);
        for ($i=0; $i<$numberPoints-1; $i++)
        {
            $this->AALine($Points[$i][0],$Points[$i][1],$Points[$i+1][0],$Points[$i+1][1],$Color);
        }
        $this->AALine($Points[$i][0],$Points[$i][1],$Points[0][0],$Points[0][1],$Color);
    }

    public function Rectangle($X1,$Y1,$X2,$Y2,$Color,$ColorFill=NULL)
    {
        if($ColorFill)
        {
            imagefilledrectangle($this->canvasImage, $X1,$Y1,$X2,$Y2, $this->RGB($ColorFill));
            if($this->RGB($ColorFill) != $this->RGB($Color)) imagerectangle($this->canvasImage, $X1,$Y1,$X2,$Y2,$this->RGB($Color));
        }
        else
            imagerectangle($this->canvasImage, $X1,$Y1,$X2,$Y2,$this->RGB($Color));
    }

    public function Ellipse($cX,$cY,$Width,$Height,$Color,$ColorFill=NULL)
    {
        if($ColorFill)
        {
            imagefilledellipse($this->canvasImage, $cX,$cY,$Width,$Height,$this->RGB($ColorFill));
            if($this->RGB($ColorFill) != $this->RGB($Color)) imageellipse($this->canvasImage, $cX,$cY,$Width,$Height,$this->RGB($Color));
        }
        else
            imageellipse($this->canvasImage, $cX,$cY,$Width,$Height,$this->RGB($Color));
    }

    public function AAEllipse($centerX,$centerY,$Width,$Height,$Color,$ColorFill=NULL)
    {
        if(!$ColorFill)
        {
            if ( $Width == 0 ) { $Width = $Height; }

            $Step     = 360 / (2 * 3.1418 * max($Width,$Height));

            for($i=0;$i<=360;$i=$i+$Step)
            {
                $X = cos($i*3.1418/180) * $Height + $centerX;
                $Y = sin($i*3.1418/180) * $Width + $centerY;
                $this->SetPixel($X,$Y,$Color);
            }
        }
        else
        {
            if ( $Width == 0 ) { $Width = $Height; }
            $Step     = 360 / (2 * 3.1418 * max($Width,$Height));

            for($i=90;$i<=270;$i=$i+$Step)
            {
                $X1 = cos($i*3.1418/180) * $Height + $centerX;
                $Y1 = sin($i*3.1418/180) * $Width + $centerY;
                $X2 = cos((180-$i)*3.1418/180) * $Height + $centerX;
                $Y2 = sin((180-$i)*3.1418/180) * $Width + $centerY;

                $this->SetPixel($X1-1,$Y1-1,$Color);
                $this->SetPixel($X2-1,$Y2-1,$Color);

                if($ColorFill)
                {
                    if ( ($Y1-1) > $centerY - max($Width,$Height) )
                        $this->Line($X1,$Y1-1,$X2-1,$Y2-1, $ColorFill);
                }
            }
        }
    }

    public function Polygon($Points,$Color,$ColorFill=NULL)
    {
        if($ColorFill)
        {
            imagefilledpolygon($this->canvasImage, $Points,count($Points),$this->RGB($ColorFill));
            if($this->RGB($ColorFill) != $this->RGB($Color)) imagepolygon($this->canvasImage, $Points,count($Points),$this->RGB($Color));
        }
        else
            imagepolygon($this->canvasImage, $Points,count($Points),$this->RGB($Color));
    }

    public function Pie($cX,$cY,$Width,$Height, $Start, $End,$Color,$ColorFill=NULL,$Style = IMG_ARC_PIE)
    {
        if($ColorFill)
        {
            imagefilledarc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($ColorFill),$Style);
            if($this->RGB($ColorFill) != $this->RGB($Color)) imagearc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($Color));
        }
        else
            imagearc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($Color));
    }

    public function Arc($cX,$cY,$Width,$Height, $Start, $End,$Color,$ColorFill=NULL,$Style = IMG_ARC_CHORD)
    {
        if($ColorFill)
        {
            imagefilledarc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($ColorFill),$Style);
            if($this->RGB($ColorFill) != $this->RGB($Color)) imagearc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($Color));
        }
        else
            imagearc($this->canvasImage, $cX,$cY,$Width,$Height, $Start, $End,$this->RGB($Color));
    }

    public function AABezier($Points,$Color)
    {
        $numPoints = count($Points);
        $numCurves = ($numPoints-1) / 3;
        $numReqPoints = $numCurves*3 + 1;

        if ($numPoints >= $numReqPoints)
        {
            for ($i=0; $i<$numCurves; $i++)
            {
                $startPoint = $Points[i*3];
                $controlPoint1 = $Points[i*3+1];
                $controlPoint2 = $Points[i*3+2];
                $endPoint = $Points[i*3+3];

                if ($controlPoint1[1] == $controlPoint2[2])
                {
                    $this->AAALine($startPoint[0], $startPoint[1], $endPoint[0], $endPoint[1], $Color);
                }
                else
                {
                    $distance1 = sqrt(pow((float)($controlPoint1[0]-$startPoint[0]),2) + pow((float)($controlPoint1[1]-$startPoint[1]),2));
                    $distance2 = sqrt(pow((float)($controlPoint2[0]-$controlPoint1[0]),2) + pow((float)($controlPoint2[1]-$controlPoint1[1]),2));
                    $distance3 = sqrt(pow((float)($endPoint[0]-$controlPoint2[0]),2) + pow((float)($endPoint[1]-$controlPoint2[1]),2));
                    $step = 1.0 / ($distance1+$distance2+$distance3);

                    $cx = 3.0*($controlPoint1[0] - $startPoint[0]);
                    $bx = 3.0*($controlPoint2[0] - $controlPoint1[0]) - $cx;
                    $ax = $endPoint[0] - $startPoint[0] - $bx - $cx;

                    $cy = 3.0*($controlPoint1[1] - $startPoint[1]);
                    $by = 3.0*($controlPoint2[1] - $controlPoint1[1]) - $cy;
                    $ay = $endPoint[1] - $startPoint[1] - $by - $cy;

                    $oldX = $startPoint[0];
                    $oldY = $startPoint[1];
                    $k1 = 0.0;
                    for ($t=0.0; $t<=1.0; $t+=$step)
                    {
                        $xt = $ax*($t*$t*$t) + $bx*($t*$t) + $cx*$t + $startPoint[0];
                        $yt = $ay*($t*$t*$t) + $by*($t*$t) + $cy*$t + $startPoint[1];

                        $distanceX = (float)($xt - (int)($xt));
                        $distanceY = (float)($yt - (int)($yt));

                        if (((int)$xt != $oldX) && ((int)$yt != $oldY))
                        {
                            $oldX1 = $oldX;
                            $oldY1 = $oldY;

                            $k = (float)((int)$yt - $oldY) / (float)((int)$xt - $oldX);

                            if ($k != $k1)
                            {
                                $this->AALine($oldX, $oldY, (int)$xt, (int)$yt, $Color);

                                $k1 = $k;
                                $oldX = (int)$xt;
                                $oldY = (int)$yt;
                            }
                        }
                    }

                    if (((int)$xt != $oldX1) || ((int)$yt != $oldY1))
                    {
                        $dx = ((int)$xt - $oldX1);
                        $dy = ((int)$yt - $oldY1);

                        if (abs($dx) > abs($dy))
                        {
                            if ($dy > 0)
                                $this->AALine($oldX, $oldY, (int)$xt, (int)$yt+1, $Color);
                            else
                                $this->AALine($oldX, $oldY, (int)$xt, (int)$yt-1, $Color);
                        }
                        else
                        {
                            if ($dx > 0)
                                $this->AALine($oldX, $oldY, (int)$xt+1, (int)$yt, $Color);
                            else
                                $this->AALine($oldX, $oldY, (int)$xt-1, (int)$yt, $Color);
                        }
                    }
                }
            }
        }
    }

    public function SaveImage($FilePathName,$Quality=null) {
	switch(strtolower(pathinfo( $FilePathName,PATHINFO_EXTENSION))) {
	    case 'png':
		imagesavealpha($this->canvasImage, true);
		return imagepng($this->canvasImage,$FilePathName,$Quality);
	    case 'jpeg':
		imagesavealpha($this->canvasImage, true);
		return imagejpeg($this->canvasImage,$FilePathName,$Quality);
	    case 'gif':
		return imagegif($this->canvasImage,$FilePathName);
	}
	return false;
    }
    
    public function ImagePng()
    {
        header ("Content-type: image/png");
        imagesavealpha($this->canvasImage, true);
        imagepng($this->canvasImage);
        exit;
    }
    
    public function GetImage()
    {
        imagesavealpha($this->canvasImage, true);
        ob_start();
        imagepng($this->canvasImage);
        $image = ob_get_contents();
        ob_end_clean();
        return $image; 
    }
    
    public function &Image()
    {
        return $this->canvasImage;
    }
    
    /*
     * 
     * @return \Image\Image
     */
    public function CloneImage($Width=0,$Height=0) {
	$Width = $Width?:  $this->canvasSize[0];
	$Height = $Height?:  $this->canvasSize[1];
	
	
	$new_im = imagecreatetruecolor( $Width, $Height );
	imagealphablending( $new_im, true );
	//imagecolortransparent( $new_im, imagecolorallocatealpha( $new_im, 0, 0, 0, 127 ) );
	
	imagesavealpha( $new_im, true );
			
	if ( false !== imagecopyresampled( $new_im, $this->canvasImage, 0, 0, 0, 0, $Width, $Height, $this->canvasSize[0], $this->canvasSize[1] ) ) {
	    $cImg = new Image();
	    $cImg->SetImage($new_im, $Width, $Height);
	    return $cImg;
	}
	
	return false;
    }
}

/**
 * @param string $file
 * @return \Image\Image
 */
function LoadImage($file) {
    $Canvas = new Image;
    $Canvas->LoadImage($file);
    return $Canvas;
}
