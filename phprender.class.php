<?php
/**
 * @author rongye@staff.sina.com.cn
 * 用法：
 * 模板参考模板  "./templates/index.html"
 * include_once 'template.class.php';
 * $render = new Render('templates');
 * $render->setCaching(true);
 * $variable = array('a'=>8,'b'=>4,'c'=>8,'name'=>'新浪无线','user'=>array('a','b','c','d','e','f','g'));
 * $render->index($variable);
 * */
class Render{
	
	private $path = '';
	private $tmplname = '';
	private $caching = false;
	private $memcache = null;
	
	public function __construct($path)
	{
		$this->path = $path;
		$this->memcache = memcache_init();

	}
	public function __destruct()
	{         
	}	
	public function setCaching($status)
	{		
		$this->caching= $status;
	}
	
	private function pre_process($html)
	{
	    if(preg_match_all("/\{\{include\s+(.+?)\}\}/",$html, $matches)>0) 
	    {
            foreach($matches[1] as $name) 
            {
            	$tmp_data = $this->getfile($this->path.'/'.$name.'.html');
            	$html = preg_replace('/\{\{include\s+'.$name.'\}\}/', $tmp_data,$html);            	
            }
        }
		return $html;
	}
	
	private function getTmpl($name)
	{
        $html = $this->getfile( $this->path.'/'.$name.'.html' );
        return  $this->normalize($this->pre_process($html));
	}
	
	private function getfile($fileName)
	{
		if($this->caching)
		{
			if(!($html = memcache_get($this->memcache,md5($fileName))))
			{
				$html =  file_get_contents($fileName);
				memcache_set($this->memcache,md5($fileName),$html);
			}
		}
		else
		{
			$html =  file_get_contents($fileName);
		}
		return $html;
	}
	
	private function parse($parameters)
	{		
		if($this->caching && memcache_get($this->memcache,md5($this->tmplname.'.func')) )
		{
			$nfunc = $this->bulidTmplFunc('');
			return implode("\n", $nfunc($parameters));
		}
		if($this->caching)
		{
			$html = memcache_get($this->memcache,md5($this->tmplname.'.html'));
			if(!$html)
			{
				$html = $this->getTmpl($this->tmplname);
				memcache_set($this->memcache,md5($this->tmplname.'.html'),$html);
			}
		}
		else
		{
			$html = $this->getTmpl($this->tmplname);
		}
		$nfunc = $this->bulidTmplFunc(explode('\n',$html));
		return implode("\n", $nfunc($parameters));	
	}
	
	private function normalize($html)
	{
		$html = preg_replace('/\r\n/','\n',$html);
		$html = preg_replace('/\r/','\n',$html);
		$html = preg_replace('/\n/','\n',$html);
		//$html = preg_replace('/"/','\\"',$html);
		$html = preg_replace("/'/","\'",$html);
		return $html;
	}

	private function bulidTmplFunc($lines)
	{
		if($this->caching)
		{
			$func = memcache_get($this->memcache,md5($this->tmplname.'.func'));
			if($func)
			{
				return create_function('$parameters',$func);
			}	
		}	
		$func = 'extract($parameters);$__=array();';
		$patt = '/\{\{(\/?)(\w+)(.*?)\}\}/';
		for($i=0;$i<count($lines);$i++)
		{
			if( preg_match($patt,$lines[$i],$matches) > 0 )
			{
				if( $matches[1] == '' )
				{
					if( $matches[2] == "elseif" )
					{
						$func .="}elseif".$matches[3];
					}
					elseif( $matches[2] == "else" )
					{
						$func .="}else".$matches[3];
					}
					else
					{
						$func .= $matches[2].$matches[3] ;
					}						
					$func .= "{";
				}	
				else
				{
					$func .= "}";
				}	
			}	
			else
			{
				$lines[$i] = preg_replace('/\${(.*?)}/','\'.\$$1.\'',$lines[$i]);
				$func .= '$__[] = \''.$lines[$i].'\';';
			}
		}			
		$func  .= 'return $__;';
		//echo $func;exit;
		if($this->caching)
		{	
			memcache_set($this->memcache,md5($this->tmplname.'.func'),$func);
		}
		return create_function('$parameters',$func);	
	}
	public function __call($name,$arg)
	{
		$this->tmplname = $name;
		$html = $this->parse($arg[0]);
        //Header("Content-type: text/html;charset=utf-8");
        Header("Content-type: text/html");
	    Header("Cache-Control: no-cache, must-revalidate");
	    Header("Pragma: no-cache");	
	    //$html = stripslashes($html);
	    echo $html;		
	}
}



?>
