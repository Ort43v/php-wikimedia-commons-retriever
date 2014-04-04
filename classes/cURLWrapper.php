<?php
	
	abstract class cURLWrapper {
		protected $curl;
		
		protected function setCurlOption($option, $value) {
			$name = $this->getCurlOptionName($option);
			if (method_exists($this, 'error')) {
				$this->error("cURL option $name set: '" . print_r($value, true) . "'");
			}
			curl_setopt($this->curl, $option, $value);
		}
		
		protected function getCurlOptionName($option) {
			switch ($option) {
				// boolean
				case CURLOPT_AUTOREFERER: $name = 'CURLOPT_AUTOREFERER'; break;
				case CURLOPT_BINARYTRANSFER: $name = 'CURLOPT_BINARYTRANSFER'; break;
				case CURLOPT_COOKIESESSION: $name = 'CURLOPT_COOKIESESSION'; break;
				case CURLOPT_CERTINFO: $name = 'CURLOPT_CERTINFO'; break;
				case CURLOPT_CRLF: $name = 'CURLOPT_CRLF'; break;
				case CURLOPT_DNS_USE_GLOBAL_CACHE: $name = 'CURLOPT_DNS_USE_GLOBAL_CACHE'; break;
				case CURLOPT_FAILONERROR: $name = 'CURLOPT_FAILONERROR'; break;
				case CURLOPT_FILETIME: $name = 'CURLOPT_FILETIME'; break;
				case CURLOPT_FOLLOWLOCATION: $name = 'CURLOPT_FOLLOWLOCATION'; break;
				case CURLOPT_FORBID_REUSE: $name = 'CURLOPT_FORBID_REUSE'; break;
				case CURLOPT_FRESH_CONNECT: $name = 'CURLOPT_FRESH_CONNECT'; break;
				case CURLOPT_FTP_USE_EPRT: $name = 'CURLOPT_FTP_USE_EPRT'; break;
				case CURLOPT_FTP_USE_EPSV: $name = 'CURLOPT_FTP_USE_EPSV'; break;
				case CURLOPT_FTP_CREATE_MISSING_DIRS: $name = 'CURLOPT_FTP_CREATE_MISSING_DIRS'; break;
				case CURLOPT_FTPAPPEND: $name = 'CURLOPT_FTPAPPEND'; break;
				//case CURLOPT_FTPASCII: $name = 'CURLOPT_FTPASCII'; break;
				case CURLOPT_FTPLISTONLY: $name = 'CURLOPT_FTPLISTONLY'; break;
				case CURLOPT_HEADER: $name = 'CURLOPT_HEADER'; break;
				case CURLINFO_HEADER_OUT: $name = 'CURLINFO_HEADER_OUT'; break;
				case CURLOPT_HTTPGET: $name = 'CURLOPT_HTTPGET'; break;
				case CURLOPT_HTTPPROXYTUNNEL: $name = 'CURLOPT_HTTPPROXYTUNNEL'; break;
				//case CURLOPT_MUTE: $name = 'CURLOPT_MUTE'; break;
				case CURLOPT_NETRC: $name = 'CURLOPT_NETRC'; break;
				case CURLOPT_NOBODY: $name = 'CURLOPT_NOBODY'; break;
				case CURLOPT_NOPROGRESS: $name = 'CURLOPT_NOPROGRESS'; break;
				case CURLOPT_NOSIGNAL: $name = 'CURLOPT_NOSIGNAL'; break;
				case CURLOPT_POST: $name = 'CURLOPT_POST'; break;
				case CURLOPT_PUT: $name = 'CURLOPT_PUT'; break;
				case CURLOPT_RETURNTRANSFER: $name = 'CURLOPT_RETURNTRANSFER'; break;
				case CURLOPT_SSL_VERIFYPEER: $name = 'CURLOPT_SSL_VERIFYPEER'; break;
				case CURLOPT_TRANSFERTEXT: $name = 'CURLOPT_TRANSFERTEXT'; break;
				case CURLOPT_UNRESTRICTED_AUTH: $name = 'CURLOPT_UNRESTRICTED_AUTH'; break;
				case CURLOPT_UPLOAD: $name = 'CURLOPT_UPLOAD'; break;
				case CURLOPT_VERBOSE: $name = 'CURLOPT_VERBOSE'; break;
				
				// integer
				case CURLOPT_BUFFERSIZE: $name = 'CURLOPT_BUFFERSIZE'; break;
				case CURLOPT_CONNECTTIMEOUT: $name = 'CURLOPT_CONNECTTIMEOUT'; break;
				case CURLOPT_CONNECTTIMEOUT_MS: $name = 'CURLOPT_CONNECTTIMEOUT_MS'; break;
				case CURLOPT_DNS_CACHE_TIMEOUT: $name = 'CURLOPT_DNS_CACHE_TIMEOUT'; break;
				case CURLOPT_FTPSSLAUTH: $name = 'CURLOPT_FTPSSLAUTH'; break;
				case CURLOPT_HTTP_VERSION: $name = 'CURLOPT_HTTP_VERSION'; break;
				case CURLOPT_HTTPAUTH: $name = 'CURLOPT_HTTPAUTH'; break;
				case CURLOPT_INFILESIZE: $name = 'CURLOPT_INFILESIZE'; break;
				case CURLOPT_LOW_SPEED_LIMIT: $name = 'CURLOPT_LOW_SPEED_LIMIT'; break;
				case CURLOPT_LOW_SPEED_TIME: $name = 'CURLOPT_LOW_SPEED_TIME'; break;
				case CURLOPT_MAXCONNECTS: $name = 'CURLOPT_MAXCONNECTS'; break;
				case CURLOPT_MAXREDIRS: $name = 'CURLOPT_MAXREDIRS'; break;
				case CURLOPT_PORT: $name = 'CURLOPT_PORT'; break;
				case CURLOPT_PROTOCOLS: $name = 'CURLOPT_PROTOCOLS'; break;
				case CURLOPT_PROXYAUTH: $name = 'CURLOPT_PROXYAUTH'; break;
				case CURLOPT_PROXYPORT: $name = 'CURLOPT_PROXYPORT'; break;
				case CURLOPT_PROXYTYPE: $name = 'CURLOPT_PROXYTYPE'; break;
				case CURLOPT_REDIR_PROTOCOLS: $name = 'CURLOPT_REDIR_PROTOCOLS'; break;
				case CURLOPT_RESUME_FROM: $name = 'CURLOPT_RESUME_FROM'; break;
				case CURLOPT_SSL_VERIFYHOST: $name = 'CURLOPT_SSL_VERIFYHOST'; break;
				case CURLOPT_SSLVERSION: $name = 'CURLOPT_SSLVERSION'; break;
				case CURLOPT_TIMECONDITION: $name = 'CURLOPT_TIMECONDITION'; break;
				case CURLOPT_TIMEOUT: $name = 'CURLOPT_TIMEOUT'; break;
				case CURLOPT_TIMEOUT_MS: $name = 'CURLOPT_TIMEOUT_MS'; break;
				case CURLOPT_TIMEVALUE: $name = 'CURLOPT_TIMEVALUE'; break;
				case CURLOPT_MAX_RECV_SPEED_LARGE: $name = 'CURLOPT_MAX_RECV_SPEED_LARGE'; break;
				case CURLOPT_MAX_SEND_SPEED_LARGE: $name = 'CURLOPT_MAX_SEND_SPEED_LARGE'; break;
				case CURLOPT_SSH_AUTH_TYPES: $name = 'CURLOPT_SSH_AUTH_TYPES'; break;
				case CURLOPT_IPRESOLVE: $name = 'CURLOPT_IPRESOLVE'; break;
				
				// string
				case CURLOPT_CAINFO: $name = 'CURLOPT_CAINFO'; break;
				case CURLOPT_CAPATH: $name = 'CURLOPT_CAPATH'; break;
				case CURLOPT_COOKIE: $name = 'CURLOPT_COOKIE'; break;
				case CURLOPT_COOKIEFILE: $name = 'CURLOPT_COOKIEFILE'; break;
				case CURLOPT_COOKIEJAR: $name = 'CURLOPT_COOKIEJAR'; break;
				case CURLOPT_CUSTOMREQUEST: $name = 'CURLOPT_CUSTOMREQUEST'; break;
				case CURLOPT_EGDSOCKET: $name = 'CURLOPT_EGDSOCKET'; break;
				case CURLOPT_ENCODING: $name = 'CURLOPT_ENCODING'; break;
				case CURLOPT_FTPPORT: $name = 'CURLOPT_FTPPORT'; break;
				case CURLOPT_INTERFACE: $name = 'CURLOPT_INTERFACE'; break;
				case CURLOPT_KEYPASSWD: $name = 'CURLOPT_KEYPASSWD'; break;
				case CURLOPT_KRB4LEVEL: $name = 'CURLOPT_KRB4LEVEL'; break;
				case CURLOPT_POSTFIELDS: $name = 'CURLOPT_POSTFIELDS'; break;
				case CURLOPT_PROXY: $name = 'CURLOPT_PROXY'; break;
				case CURLOPT_PROXYUSERPWD: $name = 'CURLOPT_PROXYUSERPWD'; break;
				case CURLOPT_RANDOM_FILE: $name = 'CURLOPT_RANDOM_FILE'; break;
				case CURLOPT_RANGE: $name = 'CURLOPT_RANGE'; break;
				case CURLOPT_REFERER: $name = 'CURLOPT_REFERER'; break;
				case CURLOPT_SSH_HOST_PUBLIC_KEY_MD5: $name = 'CURLOPT_SSH_HOST_PUBLIC_KEY_MD5'; break;
				case CURLOPT_SSH_PUBLIC_KEYFILE: $name = 'CURLOPT_SSH_PUBLIC_KEYFILE'; break;
				case CURLOPT_SSH_PRIVATE_KEYFILE: $name = 'CURLOPT_SSH_PRIVATE_KEYFILE'; break;
				case CURLOPT_SSL_CIPHER_LIST: $name = 'CURLOPT_SSL_CIPHER_LIST'; break;
				case CURLOPT_SSLCERT: $name = 'CURLOPT_SSLCERT'; break;
				case CURLOPT_SSLCERTPASSWD: $name = 'CURLOPT_SSLCERTPASSWD'; break;
				case CURLOPT_SSLCERTTYPE: $name = 'CURLOPT_SSLCERTTYPE'; break;
				case CURLOPT_SSLENGINE: $name = 'CURLOPT_SSLENGINE'; break;
				case CURLOPT_SSLENGINE_DEFAULT: $name = 'CURLOPT_SSLENGINE_DEFAULT'; break;
				case CURLOPT_SSLKEY: $name = 'CURLOPT_SSLKEY'; break;
				case CURLOPT_SSLKEYPASSWD: $name = 'CURLOPT_SSLKEYPASSWD'; break;
				case CURLOPT_SSLKEYTYPE: $name = 'CURLOPT_SSLKEYTYPE'; break;
				case CURLOPT_URL: $name = 'CURLOPT_URL'; break;
				case CURLOPT_USERAGENT: $name = 'CURLOPT_USERAGENT'; break;
				case CURLOPT_USERPWD: $name = 'CURLOPT_USERPWD'; break;
				
				// array
				case CURLOPT_HTTP200ALIASES: $name = 'CURLOPT_HTTP200ALIASES'; break;
				case CURLOPT_HTTPHEADER: $name = 'CURLOPT_HTTPHEADER'; break;
				case CURLOPT_POSTQUOTE: $name = 'CURLOPT_POSTQUOTE'; break;
				case CURLOPT_QUOTE: $name = 'CURLOPT_QUOTE'; break;
				
				// stream resource
				case CURLOPT_FILE: $name = 'CURLOPT_FILE'; break;
				case CURLOPT_INFILE: $name = 'CURLOPT_INFILE'; break;
				case CURLOPT_STDERR: $name = 'CURLOPT_STDERR'; break;
				case CURLOPT_WRITEHEADER: $name = 'CURLOPT_WRITEHEADER'; break;
				
				// callable
				case CURLOPT_HEADERFUNCTION: $name = 'CURLOPT_HEADERFUNCTION'; break;
				case CURLOPT_PASSWDFUNCTION: $name = 'CURLOPT_PASSWDFUNCTION'; break;
				case CURLOPT_PROGRESSFUNCTION: $name = 'CURLOPT_PROGRESSFUNCTION'; break;
				case CURLOPT_READFUNCTION: $name = 'CURLOPT_READFUNCTION'; break;
				case CURLOPT_WRITEFUNCTION: $name = 'CURLOPT_WRITEFUNCTION'; break;
				
				// other
				case CURLOPT_SHARE: $name = 'CURLOPT_SHARE'; break; // a result of curl_share_init()
				
				default: $name = 'unknown_curl_option';
			}
			
			return $name;
		}
		
		protected function curlInit() {
			return $this->curl = curl_init();
		}
		
		protected function curlExec() {
			return curl_exec($this->curl);
		}
		
		protected function curlErrno() {
			return curl_errno($this->curl);
		}
		
		protected function curlError() {
			return curl_error($this->curl);
		}
		
		protected function curlClose() {
			return curl_close($this->curl);
		}
	}
	