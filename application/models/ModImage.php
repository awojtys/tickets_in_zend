<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Resize
 *
 * @author awojtys
 */
class Application_Model_ModImage
{
    protected $_ratio_size;
    protected $_center_size;
    protected $_max_size;
    protected $_all_size;
    protected $_image_files;
    
    protected function getMaxSize()
    {
        $config = new Application_Model_Config();
        $configs = $config -> getAllConfig();
        
        return $this->_max_size = array('max_width' => $configs['Avatar_Width'], 'max_height' => $configs['Avatar_Height']);
    }
    
    public function renameImage($id, $file)
    {
        $originalFilename = pathinfo($file);
        $newName = $id . '.' . $originalFilename['extension'];
        
        return $newName;
    }
    
    public function resizeImage($id, $change = null) 
    {
        //pobieranie wielkości avatarów
        $size = $this->mergeSize($id);
        
        //pobranie danych usera
        $user = new Application_Model_Users();
        $mapper = new Application_Model_UsersMapper();
        $mapper ->findUserData($id, $user);
        
        $extension = explode('.', $user->getAvatar());
        if($extension[0] == 'none')
        {
            $FilePath = APPLICATION_PATH . '/../public/avatars/original/none.' . $extension[1];
        }
        else
        {
            $FilePath = APPLICATION_PATH . '/../public/avatars/original/' . $id . '.' . $extension[1];
        }
        $imagick = new Imagick($FilePath);
        
      //  var_dump($size);die();
        $FileArray = pathinfo($FilePath);
        foreach($size as $size)
        {
            $imagick ->readimage($FilePath);
            $geometry = $imagick ->getimagegeometry();
            $width = $geometry['width'];
            $height = $geometry['height'];

            //$this->_ratio($height, $width, $size['width'], $size['height']);

            $imagick ->resizeImage($size['width'], $size['height'],Imagick::FILTER_LANCZOS,1, true);
            //var_dump($size['width']);die();
            if($extension[0] == 'none')
            {
                $imagick ->writeimage($FileArray['dirname'] . '/../' . $FileArray['basename']);
            }
            else
            {
                $imagick ->writeimage($FileArray['dirname'] . '/../' . $FileArray['filename'] . '.' . $size['width'] . 'x' . $size['height'] . '.' . $FileArray['extension']);
            }
            
        }
        $imagick ->clear();
        $imagick ->destroy();
    }

    protected function _ratio($source_image_height, $source_image_width, $max_width = null, $max_height = null)
    {
        if($max_width == null && $max_height == null)
        {
            $max_size = $this->getMaxSize();
            $max_width = $max_size['max_width'];
            $max_height = $max_size['max_height'];
        }
        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $max_width / $max_height;
        if ($source_image_width <= $max_width && $source_image_height <= $max_height) {
        $thumbnail_image_width = $source_image_width;
        $thumbnail_image_height = $source_image_height;
        } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
        $thumbnail_image_width = (int) ($max_height * $source_aspect_ratio);
        $thumbnail_image_height = $max_height;
        } else {
        $thumbnail_image_width = $max_width;
        $thumbnail_image_height = (int) ($max_width / $source_aspect_ratio);
        }
        
        return $this->_ratio_size = array('height' => $thumbnail_image_height, 'width' => $thumbnail_image_width);
    }
    
    protected function _centerImage($max_width = null, $max_height = null)
    {
        if($max_width == null && $max_height == null)
        {
            $max_size = $this->getMaxSize();
            $max_width = $max_size['max_width'];
            $max_height = $max_size['max_height'];
        }
        
        if($max_height == $this->_ratio_size['height'] && $max_width == $this->_ratio_size['width'])
        {
            return $this->_center_size = array('center_width_start' => 0, 'center_height_start' => 0);
        }
        elseif($max_height > $this->_ratio_size['height'] && $max_width == $this->_ratio_size['width'])
        {
            $center_height = ($max_height - $this->_ratio_size['height'])/2;
            return $this->_center_size = array('center_width_start' => 0, 'center_height_start' => $center_height);
        }
        elseif ($max_width > $this->_ratio_size['width'] && $max_height == $this->_ratio_size['height']) 
        {
            $center_width = ($max_width - $this->_ratio_size['width'])/2;
            return $this->_center_size = array('center_width_start' => $center_width, 'center_height_start' => 0);
        }
        elseif($max_height > $this->_ratio_size['height'] && $max_width > $this->_ratio_size['width'])
        {
            $center_height = ($max_height - $this->_ratio_size['height'])/2;
            $center_width = ($max_width - $this->_ratio_size['width'])/2;
            return $this->_center_size = array('center_width_start' => $center_width, 'center_height_start' => $center_height);
        }
    }
    
    public function delete($id)
    {
        $destination_path = APPLICATION_PATH . '/../public/avatars/original/';
        @unlink($destination_path . $id . '.png');
        @unlink($destination_path . $id . '.jpg');
        @unlink($destination_path . $id . '.jpeg');
        @unlink($destination_path . $id . '.gif');
        
        $copy_path = APPLICATION_PATH . '/../public/avatars/';
        
        foreach($this->returnImageFiles($id) as $FileName)
        {
            unlink($copy_path . $FileName);
        }
    }
    
    protected function returnImageFiles($id)
    {
        $images = array();
        $katalog=APPLICATION_PATH . '/../public/avatars/'; //przypisanie do zmiennej jakiegos katalogu z plikami do wyswietlenia
        $dir=opendir($katalog); //otwarcie katalogu i przypisanie do zmiennej
        while($nazwa_pliku=readdir($dir)) //petla przypisujaca do zmiennej zawartosc katalogu
        {
            if(strstr($nazwa_pliku, $id .'.')) //pomijaj plikow “.” i “..”
            {
                $images[] = $nazwa_pliku;
            }
        }
        closedir($dir); //zamkniecie katalogu z plikami do wyswieltenia
        
        return $this->_image_files = $images;
    }
    
    protected function returnSize($id)
    {
        foreach ($this->returnImageFiles($id) as $value)
        {
            $name = explode('.', $value);
            $name2 = explode('x', $name[1]);
            $image_size[] = array('width' => $name2[0], 'height' => $name2[1]);
        }
        return $this->_all_size = $image_size;
    }
    
    protected function mergeSize($id)
    {
        $max_size = $this->getMaxSize();
        $size = $this->returnSize($id);
        
        $size[] = array('width' => $max_size['max_width'], 'height' => $max_size['max_height']);
        
        return $size;
    }
}

?>
