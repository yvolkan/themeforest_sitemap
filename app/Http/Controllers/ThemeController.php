<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\siteMap;

class ThemeController extends Controller
{
    protected $siteMap;
    protected $siteMapPath;
    
    public function processSiteMap(){
        $this->processRun();        
        return "Process success.";
    }
    
    private function processRun(){
        $this->downloadSiteMap();
        $this->extractSiteMap();
        $this->readSiteMap();
    }
    
    private function setSiteMap( $siteMap = 'https://s3.envato.com/sitemaps/themeforest.xml.gz' ){
        $this->siteMap = $siteMap;
        return $this;
    }
    
    public function getSiteMap( $onlyFileName = false ){
        if ( !$this->siteMap ){
            $this->setSiteMap();
        }
        if ( $onlyFileName ){            
            $parseURL = parse_url( $this->siteMap );
            return date('Ymd') . '-' . last( explode('/', $parseURL['path'] ) );
        } else {            
            return $this->siteMap;
        }
    }
    
    private function setSiteMapPath( $path = '' ){
        $this->siteMapPath = $path;
        return $this;
    }

    public function getSiteMapPath( $extract = false ){
        return ( $extract ) ? str_replace('.gz', '', $this->siteMapPath) : $this->siteMapPath;
    }

    private function downloadSiteMap(){        
        try {            
            $siteMapPath = base_path() . '\temp\\' . $this->getSiteMap( true );            
            if ( file_exists($siteMapPath) ){
                $this->setSiteMapPath( $siteMapPath );
                return true;
            }
            
            if ( !$siteMapContent = file_get_contents( $this->getSiteMap() ) ){
                throw new \Exception('Invalid site map url. Error Code: 10001');
            }            
        
            if ( file_put_contents( $siteMapPath, $siteMapContent) ){
                $this->setSiteMapPath( $siteMapPath );
            } else {
                throw new \Exception('Can\'t access directory. Error Code: 10002');
            }
        } catch (\Exception $ex) {
            dd( $ex->getMessage() );
        }
        echo "-Download site map : " . $this->getSiteMap() . " \r\n";
    }
    
    private function extractSiteMap(){
        if ( !$siteMapPath = $this->getSiteMapPath() ){
            throw new \Exception('You must start downloadSiteMap function before this. Error Code: 10003');
        }
        
        $buffer_size = 4096;
        $file = gzopen( $siteMapPath, 'rb' );
        $out_file = fopen( $this->getSiteMapPath( true ), 'wb');
        
        while ( !gzeof($file) ) {
            fwrite($out_file, gzread($file, $buffer_size));
        }
        
        fclose($out_file);
        gzclose($file);
        echo "-Extract site map : " . $this->getSiteMap() . " \r\n";
    }
    
    private function readSiteMap(){
        if ( !$siteMapPath = $this->getSiteMapPath( true ) ){
            throw new \Exception('You must start extractSiteMap function before this. Error Code: 10004');
        }
        
        $mainSiteMap = true;
        if ( strpos($siteMapPath, 'themeforest.xml') === FALSE ){
            $mainSiteMap = false;
        }
        
        $xml = simplexml_load_file( $siteMapPath );
        if ( $xml ){            
            foreach( $xml as $key => $value ){
                if ( $mainSiteMap ){
                    $this->setSiteMap( $value->loc );
                    $this->processRun();
                } else {
                    siteMap::create([
                        'url' => $value->loc,
                        'status' => 'waiting'
                    ]);
                }
            }            
        }
        echo "-Read site map : " . $this->getSiteMap() . " \r\n";
    }
}
