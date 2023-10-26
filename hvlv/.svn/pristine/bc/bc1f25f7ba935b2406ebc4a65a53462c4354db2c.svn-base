<?php
class XMLCreator
{
    private $params = [];
    public function __construct($thisParams=false)
    {
        $this->params = $thisParams;
    }
	public function create($filename,$parameters)
    {
        $dom = new DOMDocument('1.0');
        $dom->formatOutput = true;

        foreach ($parameters as $key => $param) 
        {

            foreach($this->params as $v1) {
               if(preg_match('/^'.$v1.'[\d]*/', $key))
                {
                    $key = $v1;
                }
            }

            if(preg_match('/^table[\d]*/', $key))
            {
                $key = 'table';
            }

            if(preg_match('/^detail[\d]*/', $key))
            {
                $key = 'table1';
            }

            if(preg_match('/^DDDD[\d]*/', $key))
            {
                $key = 'table2';
            }
            if(is_array($param))
            {
                $childNode = $this->addNode($key,$param,$dom);
                $dom->appendChild($childNode);

            }else
            {
                $childNode = $dom->createElement($key);
                if($param!="")
                {
                    $text = $dom->createTextNode($param);
                    $childNode->appendChild($text);
                }
                $dom->appendChild($childNode);
            }
        }
        
        $dom->save($filename);
        return $dom;
    }

    private function addNode($parentKey,$params,$dom)
    {
        $thisNode = $dom->createElement($parentKey);
        foreach ($params as $key => $param) {

            foreach ($this->params as $v1) {
               if(preg_match('/^'.$v1.'[\d]*/', $key))
                {
                    $key = $v1;
                }
            }


            if(preg_match('/^table[\d]*/', $key))
            {
                $key = 'table';
            }

            if(preg_match('/^detail[\d]*/', $key))
            {
                $key = 'table1';
            }

            if(preg_match('/^DDDD[\d]*/', $key))
            {
                $key = 'table2';
            }
            if(is_array($param))
            {
                $childNode = $this->addNode($key,$param,$dom);
                $thisNode->appendChild($childNode);
            }else
            {
                $childNode = $dom->createElement($key);
                if($param!="")
                {
                    $text = $dom->createTextNode($param);
                    $childNode->appendChild($text);
                }
                $thisNode->appendChild($childNode);
            }
        }
        return $thisNode;
    }



    public function createSF($parameters)
    {
        $dom = new DOMDocument('1.0');
        $dom->formatOutput = true;

        foreach ($parameters as $key => $param) 
        {
            $key = explode('|', $key)[0];
            if(is_array($param))
            {
                $attributes = isset($param['myAttributes'])?$param['myAttributes']:[];
                if(count($attributes)>0)unset($param['myAttributes']);
                $childNode = $this->addNodeSF($key,$param,$dom);
                $dom->appendChild($childNode);
                foreach ($attributes as $key => $value) {
                   $domAttribute =$dom->createAttribute($key);
                   $domAttribute->value = $value;
                    $childNode->appendChild($domAttribute);
                }
                
            }else
            {
                $attributes = isset($param['myAttributes'])?$param['myAttributes']:[];
                if(count($attributes)>0)unset($param['myAttributes']);
                $childNode = $dom->createElement($key);
                if($param!="")
                {
                    $text = $dom->createTextNode($param);
                    $childNode->appendChild($text);
                }
                $dom->appendChild($childNode);
                foreach ($attributes as $key => $value) {
                   $domAttribute =$dom->createAttribute($key);
                   $domAttribute->value = $value;
                   $childNode->appendChild($domAttribute);
                }
            }
        }
        $xml =  $dom->saveXML();
        $xml = str_replace('  ', '', $xml);
         $xml = str_replace("\n", '', $xml);
        return $xml;
    }

    private function addNodeSF($parentKey,$params,$dom)
    {
        $thisNode = $dom->createElement($parentKey);
        foreach ($params as $key => $param) {
            $key = explode('|', $key)[0];
            if(is_array($param))
            {
                $attributes = isset($param['myAttributes'])?$param['myAttributes']:[];
                if(count($attributes)>0)unset($param['myAttributes']);
                $childNode = $this->addNodeSF($key,$param,$dom);
                $thisNode->appendChild($childNode);
                foreach ($attributes as $key => $value) {
                    $domAttribute =$dom->createAttribute($key);
                    $value = str_replace('&', '&amp;', $value);
                    $domAttribute->value = $value;
                    $childNode->appendChild($domAttribute);
                }
            }else
            {
                $attributes = isset($param['myAttributes'])?$param['myAttributes']:[];
                if(count($attributes)>0)unset($param['myAttributes']);
                $childNode = $dom->createElement($key);
                if($param!="")
                {
                    $text = $dom->createTextNode($param);
                    $childNode->appendChild($text);
                }
                $thisNode->appendChild($childNode);
                foreach ($attributes as $key => $value) {
                    $domAttribute =$dom->createAttribute($key);
                    $domAttribute->value = $value;
                    $childNode->appendChild($domAttribute);
                }
            }
        }
        return $thisNode;
    }


}

?>