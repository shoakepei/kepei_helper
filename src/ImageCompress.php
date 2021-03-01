<?php 
/**
 * 图片压缩类：通过缩放来压缩。
* 如果要保持源图比例，把参数$percent保持为1即可。
* 即使原比例压缩，也可大幅度缩小。数码相机4M图片。也可以缩为700KB左右。如果缩小比例，则体积会更小。
* Author @XiaoTaiTai  2018-8-6
* 结果：可保存、可直接显示。
*/

namespace kepei_helper;

class ImageCompress {
    private $src;
    private $image;
    private $imageinfo;
    private $percent = 0.5; // 尺寸
    /**
     * 图片压缩
     * @param $src 源图
     * @param float $percent  压缩尺寸比例
     */
    public function __construct($src, $percent=1)
    {
        $this->src = $src;
        $this->percent = $percent;
    }
    /** 高清压缩图片
     * @param string $saveName  提供图片名（可不带扩展名，用源图扩展名）用于保存。或不提供文件名直接显示
     */
    // public function compressImg($saveName='')
    // {
    //     $this->openImage();
    //     // dump($saveName);
    //     // dump(!empty($saveName));
    //     if(!empty($saveName)){
    //         $this->saveImage($saveName);  //保存
    //     }else{
    //         // die;
    //         $this->showImage();
    //     }
    // }
    /**
     * 内部：打开图片
     */
    public function openImage()
    {
        list($width, $height, $type, $attr) = getimagesize($this->src);
        $this->imageinfo = array(
            'width'=>$width,
            'height'=>$height,
            'type'=>image_type_to_extension($type,false),
            'attr'=>$attr
        );
        // dump($this->src);
        // dump($this->imageinfo);
        // die;
        $fun = "imagecreatefrom".$this->imageinfo['type'];
        $this->image = $fun($this->src);
        // dump($this->image);die;
        $this->thumpImage();

        return $this;
    }
    /**
     * 内部：操作图片
     */
    private function thumpImage()
    {
        $new_width = $this->imageinfo['width'] * $this->percent;
        $new_height = $this->imageinfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width,$new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump,$this->image,0,0,0,0,$new_width,$new_height,$this->imageinfo['width'],$this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }
    /**
     * 输出图片:保存图片则用saveImage()
     */
    public function showImage()
    {
        header('Content-Type: image/'.$this->imageinfo['type']);
        $funcs = "image".$this->imageinfo['type'];
        $funcs($this->image);
        return $this;
    }
    /**
     * 保存图片到硬盘：
     * @param  string $dstImgName  1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     */
    public function saveImage($dstImgName = null)
    {
        if(empty($dstImgName)){
            $funcs = "image".$this->imageinfo['type'];
            return $funcs($this->image,null);
        }
        $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp','.gif'];   //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt =  strrchr($dstImgName ,".");
        $sourseExt = strrchr($this->src ,".");
        if(!empty($dstExt)) $dstExt =strtolower($dstExt);
        if(!empty($sourseExt)) $sourseExt =strtolower($sourseExt);
        //有指定目标名扩展名
        if(!empty($dstExt) && in_array($dstExt,$allowImgs)){
            $dstName = $dstImgName;
        }elseif(!empty($sourseExt) && in_array($sourseExt,$allowImgs)){
            $dstName = $dstImgName.$sourseExt;
        }else{
            $dstName = $dstImgName.$this->imageinfo['type'];
        }
        
        $funcs = "image".$this->imageinfo['type'];
        $res = $funcs($this->image,$dstName);
        // imagejpeg();
        // dump($this->image);
        // dump($dstName);
        // dump($funcs);
        // die;
        return $res;
    }
    /**
     * 销毁图片
     */
    public function __destruct(){
        imagedestroy($this->image);
    }
}