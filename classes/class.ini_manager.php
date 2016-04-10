<?php

# ini handler class
# coded by Alessandro Rosa
# e-mail : zandor_zz@yahoo.it
# site : http://alessandrorosa.altervista.org

# Copyright (C) 2008  Alessandro Rosa

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 3 of the License, or
# any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software Foundation,
# Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA

# Compiled with PHP 4.4.0

# July 24-26th 2008 : improved managements of single and double quotes by Ulrich Zdebel
# July 27th 2006 : fixed a management bug for entries including (double) quotes by Joao Borges
# August 5th 2006 : fixed a "=" term management bug by replacing all calls 
#                   to custom version of 'parse_ini_file' function member
# November 19th 2006: fixed a bug with the 'p' character handling

class ini_manager
{
	public $filename;
	public static $params = array();
	private static $ini_instance = null;
	#make clone and construct as private to prevent from generating new instances of object 
	private function __construct(){
		$this->crlf = ( strpos( strtolower($_SERVER['HTTP_USER_AGENT']), "windows", 0 ) === false ) ? "\n" : "\r\n" ;
		unset( $this->bi_a );
	}
	static function getInstance(){
		if(self::$ini_instance == NULL){
			self::$ini_instance = new ini_manager;
		}
		return self::$ini_instance;
	}
	 // Implementation of parse_ini_file
   // by Hugo Gonçalves (hugo_goncalves@portugalmail.pt)
	 
   function parse_ini_file()
	 {
	     unset( $this->bi_a );
	 
		// Allocate the result array
		$res = array();
		// Does the file exists and can we read it?
		if(file_exists($this->filename) && is_readable($this->filename))
		{
			// In the beggining we are not in a section
			$section = "";
			// Open the file
			$fd = @fopen($this->filename,"r");
			
			// Read each line
			while(!feof($fd))
			{
				// Read the line and trim it
				$line = trim(@fgets($fd, 4096 ));
				
				$len = strlen($line);
				// Only process non-blank lines
				if($len != 0)
				{
					// Only process non-comment lines
					if($line[0] != ';')
					{
						// Found a section?
						if( ( $line[0] == '[') && ($line[$len-1] == ']' ) )
						{
							// Get section name
							$section = substr($line,1,$len-2);
							// Check if the section is already included in result array						
							if(!isset($res[$section]))
							{
								// If not included create it
								$res[ $section ] = array();
							}
						}
						// Check for entries
						$pos = strpos($line,'=');
						// Found an entry
						if( $pos != false )
						{
							// get name of entry and [Joao Borges] delete any blank spaces (begin and end)
							$name = trim( substr( $line, 0, $pos ) );

							// get value of entry and [Joao Borges] delete blank spaces again
							$value = trim( substr( $line, $pos+1, $len - $pos - 1 ) );

              $value = stripslashes( $value );
              
              // follows some sort of inizialization for entries not including text
              if ( empty( $value ) ) $value = "" ;
              
              // syntax must be strictly followed !

              if ( strlen( $name ) > 0 )
              {
      							// Store entry if we are inside a section
      							if( strlen( $section ) > 0 )
      							{
        								$res[$section][$name] = $value;
      							}
      							else						
      							{
      								  $res[$name] = $value;
      							}
              }
						}
					}
				}				
			}
			// Close the file
			@fclose($fd);
		}
		return $res;
	}

      function show_ini()
      {
            $INIarray = $this->parse_ini_file();
            
            $fileCONTENTS = "" ;
            
            $c1 = 0 ;
            if ( is_array( $INIarray ) )
            {
                foreach ( $INIarray as $i => $a )
                { 
                    $c2 = 0 ;
                    if ( is_array( $a ) )
                    {
                        foreach ( $a as $n => $value )
                        { 
                            if ( $c2 == 0 ) $fileCONTENTS .= "[$i]<br/>$this->CRLF$n=$value<br/>$this->CRLF";
                            else if ( strlen( $value ) != 0 ) $fileCONTENTS .= "$n=$value<br/>$this->CRLF";
                            $c2++;
                        }
                        
                        $fileCONTENTS .= "<br/>$this->CRLF" ;
                    }
                }
                
                $fileCONTENTS = substr( $fileCONTENTS, 0, strlen( $fileCONTENTS ) - ( 5 + strlen( "<br/>" ) ) );
            }
            
            echo "<code>$fileCONTENTS</code>" ;
      }

      function save_ini()
      {
            $fileCONTENTS = "" ;
            
            $c1 = 0 ;
            
            if ( is_array( $this->bi_a ) )
            {
                foreach ( $this->bi_a as $i => $a)
                { 
                    $c2 = 0 ;
                    
                    if ( is_array( $a ) )
                    {
                        foreach ( $a as $n => $value )
                        { 
                            if ( $c2 == 0 ) $fileCONTENTS .= "[$i]$this->crlf$n=$value$this->crlf";
                            else if ( strlen( $value ) != 0 ) $fileCONTENTS .= "$n=$value$this->crlf";
                            $c2++;
                        }
                        
                        $fileCONTENTS .= $this->crlf ;
                    }
                }
                
                $hFile = @fopen( $this->filename, "w+" );
                @fwrite( $hFile, $fileCONTENTS );
                @fclose( $hFile );
                
                unset( $this->bi_a );
            }
      }

      function get_ini_array()
      {
            $INIarray = $this->parse_ini_file();

            return $INIarray ;

      }

      //////////////////////////////////////////////////////////
      function find_entry( $keyNAME, $entryNAME )
      {

            $INIarray = $this->parse_ini_file();
            
            if ( is_array( $INIarray ) )
            {
                foreach ( $INIarray as $i => $a )
                {
                    if ( is_array( $a ) )
                    {
                        foreach ( $a as $n => $value )
                        { 
                            if ( strcmp( $i, $keyNAME ) == 0 && strcmp( $n, $entryNAME ) == 0 )
                            { 
                                return true ;
                            }
                        }
                    }
                }
            }

            return false ;
      }
      //////////////////////////////////////////////////////////
      function get_entry( $keyNAME, $entryNAME )
      {
             $INIarray = $this->parse_ini_file();
      
            if ( is_array( $INIarray ) )
            {
                foreach ( $INIarray as $i => $a )
                { 
                    if ( is_array( $a ) )
                    {
                        foreach ( $a as $n => $value )
                        { 
                            if ( strcmp( $i, $keyNAME ) == 0 && strcmp( $n, $entryNAME ) == 0 )
                            { 
                                // [Ulrich Zdebel] Bugfix: doublequotes were not correctly managed
                                return stripslashes( $value ) ;
                            }
                        }
                    }
                }
            }

            return "" ;
      }
      //////////////////////////////////////////////////////////
      function add_entry( $keyNAME, $entryNAME, $entryVALUE )
      {
            if ( $this->find_entry( $this->filename, $keyNAME, $entryNAME ) )
            {
                $this->set_entry( $this->filename, $keyNAME, $entryNAME, $entryVALUE ) ;
                return ;
            }
            $INIarray = $this->parse_ini_file();
      
            $this->bi_a = array();
            
            $bKEYfound = false ;
            $bKEYadded = false ;
            
            if ( is_array( $INIarray ) )
            {
                foreach ($INIarray as $i => $a)
                { 
                    if ( is_array( $a ) )
                    {
                          foreach ($a as $n => $value)
                          { 
                              if ( strcmp( $i, $keyNAME ) == 0 ) $bKEYfound = true ;
                              
                              $this->bi_a[$i][$n] = $a[$n] ;
                          }
                      
                          if ( $bKEYfound )
                          { 
                              $this->bi_a[$i][$entryNAME] = $entryVALUE ;
                              $bKEYfound = false ;
                              $bKEYadded = true ;
                          }
                    }
                }
            }
            

          if ( !$bKEYadded ) $this->bi_a[$keyNAME][$entryNAME] = $entryVALUE ;

          $this->save_ini();
      }


      //////////////////////////////////////////////////////////
      function set_entry( $keyNAME, $entryNAME, $entryVALUE )
      {
            $INIarray = $this->parse_ini_file();
      
            $this->bi_a = array();

            if ( is_array( $INIarray ) )
            {
                foreach ($INIarray as $i => $a)
                { 
                    if ( is_array( $a ) )
                    {
                        foreach ($a as $n => $value)
                        { 
                            if ( strcmp( $i, $keyNAME ) == 0 && strcmp( $n, $entryNAME ) == 0 )
                            { 
                                $this->bi_a[$i][$n] = $entryVALUE ;
                            }
                            else $this->bi_a[$i][$n] = $a[$n] ;
                        }
                    }
                }
            }

          $this->save_ini();
      }

      //////////////////////////////////////////////////////////
      function delete_entry( $keyNAME, $entryNAME )
      {
            $INIarray = $this->parse_ini_file();
            $this->bi_a = array();
            
            if ( is_array( $INIarray ) )
            {
                foreach ($INIarray as $i => $a)
                { 
                    if ( is_array( $a ) )
                    {
                        foreach ($a as $n => $value)
                        { 
                            if ( strcmp( $i, $keyNAME ) == 0 && strcmp( $n, $entryNAME ) == 0 )
                            { 
                                // don't do anything !
                            }
                            else $this->bi_a[$i][$n] = $a[$n] ;
                        }
                    }
                }
            }

          $this->save_ini();
      }

      function delete_key($keyNAME )
      {
            $fileCONTENTS = "" ;
            $INIarray = $this->parse_ini_file();
            
            if ( is_array( $INIarray ) )
            {
                foreach ($INIarray as $i => $a)
                { 
                    $c2 = 0 ;
                    
                    if ( is_array( $a ) )
                    {
                        foreach ($a as $n => $value)
                        { 
                            if ( $c2 == 0 && strcmp( $i, $keyNAME ) == 0 ) $fileCONTENTS .= "[$i]$this->crlf";
                            $c2++ ;
                        }
                        
                        $fileCONTENTS .= $this->crlf ;
                    }
                }
            }
            
            $hFile = @fopen( $this->filename, "w+" );
            @fwrite( $hFile, $fileCONTENTS );
            @fclose( $hFile );
     }

      function delete_all_keys()
      {
            $fileCONTENTS = "" ;
            $INIarray = $this->parse_ini_file();
            
            if ( is_array( $INIarray ) )
            {
                foreach ($INIarray as $i => $a)
                { 
                    $c2 = 0 ;
                    
                    if ( is_array( $a ) )
                    {
                      foreach ($a as $n => $value)
                      { 
                          if ( $c2 == 0 ) $fileCONTENTS .= "[$i]$this->crlf";
                          $c2++ ;
                      }
                      
                      $fileCONTENTS .= $this->crlf ;
                    }
                }
            }
            
            $hFile = @fopen( $this->filename, "w+" );
            @fwrite( $hFile, $fileCONTENTS );
            @fclose( $hFile );
     }
      
      var $bi_a ;
      var $filePATH ;
      var $crlf ;
}
?>
