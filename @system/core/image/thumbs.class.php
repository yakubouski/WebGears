<?php
namespace Image;

class Thumbs 
{
    static public function Upload( $TempFile,$Mime,$Source, $DestPath, $maxWidthAndHeight=false ) 
    {
        $IsJpeg = false;
        if(!is_uploaded_file($TempFile)) return false;
        switch(strtolower($Mime)) {
            case 'image/jpeg': case 'image/jpg':
                $img = imagecreatefromjpeg( $TempFile );
                $IsJpeg = true;
            break;
            case 'image/png':
                $img = imagecreatefrompng( $TempFile );
                imagesavealpha($img,true); 
                imagealphablending($img, true); 
            break;
            case 'image/gif':
                $img = imagecreatefromgif( $TempFile );
                imagesavealpha($img,true); 
            break;
            default:
                return '';
        }
        
        $width = imagesx( $img );
        $height = imagesy( $img );
        $fileName = date('Ymdhis').sha1($Source.'|'.$TempFile);
        
        if($maxWidthAndHeight && ($width > $maxWidthAndHeight || $height > $maxWidthAndHeight)) {
            if($width>$height) {
                $new_width = $maxWidthAndHeight;
                $new_height = floor( $height * ( $maxWidthAndHeight / $width ) );
            }
            else {
                $new_height = $maxWidthAndHeight;
                $new_width = floor( $width * ( $maxWidthAndHeight / $height ) );
            }
            $tmp_img = imagecreatetruecolor( $new_width, $new_height );
            if($IsJpeg) {
                imagealphablending($tmp_img, true); 
                $transparent = imagecolorallocatealpha( $tmp_img, 0, 0, 0, 127 ); 
                imagefill( $tmp_img, 0, 0, $transparent ); 
            }

            imagesavealpha($tmp_img,true); 
            imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
            file_put_contents(\File::FullPath($DestPath.$fileName), json_encode(['name'=>$Source,'mime'=>'image/png','path'=>($DestPath.'.'.$fileName)],JSON_UNESCAPED_UNICODE));
            imagepng( $tmp_img, \File::FullPath($DestPath.'.'.$fileName) );
        }
        else {
            imagedestroy($img);
            file_put_contents(\File::FullPath($DestPath.$fileName), json_encode(['name'=>$Source,'mime'=>$Mime,'path'=>($DestPath.'.'.$fileName)],JSON_UNESCAPED_UNICODE));
            move_uploaded_file($TempFile, \File::FullPath($DestPath.'.'.$fileName));
        }
        return $DestPath.$fileName;
    }
    
    static public function Download( $Source ) 
    {
        $FileInfo = file_get_contents(\File::FullPath($Source));
        if(!empty($FileInfo)) {
            $FileInfo = json_decode($FileInfo, true);
            while (@ob_get_level()) { @ob_end_clean(); }
            header('Content-type: '.$FileInfo['mime']);
            header('Cache-Control: public');  
            header('Pragma: public'); 
            header("Cache-control: max-age=1800");
            readfile(\File::FullPath($FileInfo['path']));
        }
        exit;
    }
}
