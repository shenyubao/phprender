<?php
/**
 * @todo a php template engine
 * @version 1.0.2
 * @author rongye@staff.sina.com.cn
 * @date 2012.4.4
 * 用法：
 * 模板参考模板  "./templates/index.html"
 * include_once 'render.class.php';
 * $render = new Render('tmpl');
 * $variable = array('a'=>8,'b'=>4,'c'=>8,'name'=>'新浪无线','user'=>array('a','b','c','d','e','f','g'));
 * $render->index($variable);
 * */
class Render{
	
	private $path = '';
	private $tmplname = '';
	
	public function __construct($path)
	{
		$this->path = $path;
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
		return file_get_contents($fileName);
	}
	
	private function parse($parameters)
	{		
		$html = $this->getTmpl($this->tmplname);
		$nfunc = $this->bulidTmplFunc(explode('\n',$html));
		if(!$nfunc)
		{
		    die('模板方法名异常');
		}
		else 
		{
			return implode("\n", $nfunc($parameters));	
		}
	}
	
	private function normalize($html)
	{
		$html = preg_replace('/\r\n/','\n',$html);
		$html = preg_replace('/\r/','\n',$html);
		$html = preg_replace('/\n/','\n',$html);
		$html = preg_replace("/'/","\'",$html);
		return $html;
	}

	private function bulidTmplFunc($lines)
	{
		#$controlStatement = 0;
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
						$func .= stripslashes($matches[2].$matches[3]);
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
				$lines[$i] = preg_replace('/(?<!\$)\$\{(.*?)\}/','\'.(\$$1).\'',$lines[$i]);
				$lines[$i] = preg_replace('/\$\$\{(.*?)\}/','\${$1}',$lines[$i]);
				$func .= '$__[] = \''.stripslashes($lines[$i]).'\';';
			}
		}			
		$func  .= 'return $__;';
		//echo $func;exit;
		return create_function('$parameters',$func);	
	}
	public function __call($name,$arg)
	{
		$this->tmplname = $name;
		$html = $this->parse($arg[0]);
        Header("Content-type: text/html");
	    Header("Cache-Control: no-cache, must-revalidate");
	    Header("Pragma: no-cache");	
	    echo $html;	
	}
}



?>
