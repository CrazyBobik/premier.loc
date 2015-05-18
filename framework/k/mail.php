<?php

/**
 * Recomentded Fileinfo.so extension library for attaching files
 * Module use PHP mail for send emails
 */

class K_Mail {
    const TYPE_OCTETSTREAM = 'application/octet-stream';
	const TYPE_MULTIPART_MIXED = 'multipart/mixed';
    const TYPE_TEXT = 'text/plain';
    const TYPE_HTML = 'text/html';	

    const LINEEND = "\r\n";
	const SEPARATOR = '--';

    protected $_to = array();

    protected $_from;

    protected $_cc = array();

    protected $_bcc = array();

    protected $_headers = array();

    protected $_attachments = array();

    protected $_body;

    protected $_subject = 'nosubject';

    protected $_contentType = self::TYPE_TEXT;

    protected $_charset = 'utf-8';
	
	protected $_boundary = null;

    public function  __construct() {
        
    }
	
	public function addAttachment( $filePath, $letterFileName = null ) {
		$this->_attachments[ $filePath ] = $letterFileName;
	}
	
	/**
	 * Recomentded Fileinfo.so extension library
	 */
	private function buildMultipartEmail() {
		if ( count($this->_attachments) > 0 ) {
			$hash = md5( time() );
			
			$mixedBoundary = 'mixed-'.$hash;
			
			$this->_boundary = $mixedBoundary;
			
			$alternateBoundary = 'alt-'.$hash;

			$textBody = $this->_body;
			$textContentType = $this->_contentType;
			
			$letterBody = '';
			
			$letterBody .= 
				 self::SEPARATOR.$mixedBoundary.self::LINEEND // begin letter content
				.'Content-Type: '.$textContentType.'; charset="'.$this->_filterOther($this->_charset).'"'.self::LINEEND
				.self::LINEEND
				.$textBody.self::LINEEND;
				
			$attachedFiles = 0;
				
			foreach( $this->_attachments as $attachFileName => $letterFileName ) {
				if ( file_exists( $attachFileName ) && is_file( $attachFileName ) ) {
					$mimeType = self::TYPE_OCTETSTREAM;
					
					if ( function_exists( 'mime_content_type' ) ) {
						$mimeType = mime_content_type( $attachFileName );
					}
					
					if ( empty($mimeType) ) {
						$mimeType = self::TYPE_OCTETSTREAM;
					}
					
					$attachment = chunk_split(base64_encode(file_get_contents($attachFileName)));

					$letterBody .=
						 self::LINEEND
						.self::SEPARATOR.$mixedBoundary.self::LINEEND
						.'Content-Type: '.$mimeType.'; name="'.$this->_filterOther( basename( !empty($letterFileName) ? $letterFileName : $attachFileName ) ).'"'.self::LINEEND
						.'Content-Transfer-Encoding:base64'.self::LINEEND
						.'Content-Disposition:attachment'.self::LINEEND
						.self::LINEEND
						.$attachment.self::LINEEND;
					
					$attachedFiles++;
				}
			}
			
			// if we have attached files, than replace body, set content type & remove charset
			if ( $attachedFiles ) {
				$letterBody .= self::SEPARATOR.$mixedBoundary.self::SEPARATOR;
				
				$this->_body = $letterBody;
				$this->_contentType = self::TYPE_MULTIPART_MIXED;
				$this->_charset = null;
			}
		}
	}

    // Send to recipient(s)
    public function addTo( $email ) {
        $this->addToArray( $this->_to, $email );
    }

    public function setBody( $data, $type = self::TYPE_TEXT, $charset = 'utf-8' ) {
        $this->_contentType = $type;
        $this->_charset = $charset;
        $this->_body = $data; //mb_convert_encoding( $data, $this->_charset );
    }

    public function addHeader( $header ) {
        $this->_headers[] = $this->_filterOther($header);
    }

    public function setCharset( $charset ) {
        $this->_charset = $charset;
    }

    public function setSubject( $subject ) {
        $this->_subject = $this->_filterOther( $subject );
    }

    public function setFrom( $fromEmail ) {
        $this->_from = $fromEmail;
    }

    public function send( $from = null, $subject = null ) {
        if ( !empty($from) ) {
            $this->_from = $from;
        }
		
        if ( !empty($subject) ) {
            $this->_subject = $subject;
        }

		$this->buildMultipartEmail();
		
        mail( implode( ',', $this->_to ), $this->_subject, $this->_body, $this->buildHeaders() );
    }

    // Copy recipient(s)
    public function addCc( $email ) {
        $this->addToArray( $this->_cc, $email );
    }

    protected function addToArray( &$array, &$email ) {
        if ( is_string($email) ) {
            $array[] =  $this->_filterEmail( $email );
        }
        if ( is_array($email) ) {
            foreach( $email as &$value ) {
                $value = $this->_filterEmail( $value );
            }
            $array = array_merge( $array, array_values($email) );
        }
    }

    // Hidden copy recipient(s)
    public function addBcc( $email ) {
        $this->addToArray( $this->_bcc, $email );
    }

    protected function buildHeaders() {
        $text = '';
		
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: '.$this->_filterOther($this->_contentType)
				.( !empty($this->_charset) ? '; charset="'.$this->_filterOther($this->_charset).'"' : '' )
				.( !empty($this->_boundary) ? '; boundary="'.$this->_filterOther($this->_boundary).'"' : '' )
        );

        if ( !empty($this->_from) ) {
            $headers[] = 'From: '.$this->_filterEmail($this->_from);
        }

        if ( !empty($this->_cc) ) {
            $headers[] = 'Cc: '.implode(',', $this->_cc);
        }

        if ( !empty($this->_bcc) ) {
            $headers[] = 'Bcc: '.implode(',', $this->_bcc);
        }

        $text = implode(self::LINEEND, $headers).self::LINEEND;
        
        return $text;
    }

    /**
     * Filter of email data
     *
     * @param string $email
     * @return string
     */
    protected function _filterEmail($email)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => '',
                      ','  => '',
                      '<'  => '',
                      '>'  => '',
        );

        return strtr($email, $rule);
    }

    /**
     * Filter of name data
     *
     * @param string $name
     * @return string
     */
    protected function _filterName($name)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
                      '"'  => "'",
                      '<'  => '[',
                      '>'  => ']',
        );

        return trim(strtr($name, $rule));
    }

    /**
     * Filter of other data
     *
     * @param string $data
     * @return string
     */
    protected function _filterOther($data)
    {
        $rule = array("\r" => '',
                      "\n" => '',
                      "\t" => '',
        );

        return strtr($data, $rule);
    }
}

?>