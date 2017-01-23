<?php
namespace Image;
require_once __DIR__.'/../lib/3th/phpqrcode/qrlib.php';

class QR {
    const ECCL = 'L';
    const ECCM = 'M';
    const ECCQ = 'Q';
    const ECCH = 'H';
    
    static public function Png($Data,$EccLevel,$BlockSize=3,$Margin=0,$AsBase64Data=false) {
        if(!$AsBase64Data) {
            \QRcode::png($Data, false, $EccLevel, $BlockSize, $Margin, false);
            exit;
        }
        else {
            ob_start();
            \QRcode::png($Data, false, $EccLevel, $BlockSize, $Margin, false);
            $Image = ob_get_clean();
            return 'data:image/png;base64,'.base64_encode($Image);
        }
    }
}