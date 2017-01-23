<?php
namespace Morphology;
class PorterStemmer 
{
    private $Stem_Caching = 0;
    private $kill_predlog = true; //удалять или нет предлоги из фразы
    private $Stem_Cache = array();
    private $VOWEL = 'аеиоуыэюя';
    private $PERFECTIVEGROUND = '((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$';
    private $REFLEXIVE = '(с[яь])$';
    private $ADJECTIVE = '(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$';
    private $PARTICIPLE = '((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$';
    private $VERB = '((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$';
    private $NOUN = '(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$';
    private $RVRE = '^(.*?[аеиоуыэюя])(.*)$';
    private $DERIVATIONAL = '[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$';
    private $PREDLOG = 'и|для|в|на|под|из|с|по';

    private function ru_s(&$s, $re, $to) { $orig = $s; $s = mb_ereg_replace($re, $to, $s); return $orig !== $s; }

    private function ru_m($s, $re) { return mb_ereg_match($re, $s); }
    
    private function ru_stem_word($word) 
    {
        static $ru_cached_words = array();
        
        $word = mb_strtolower($word);
        
        if(isset($ru_cached_words[$word])) return $ru_cached_words[$word];
        
        $word = str_replace( 'ё', 'е', $word );
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!mb_ereg($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;

          # Step 1
          if (!$this->ru_s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->ru_s($RV, $this->REFLEXIVE, '');

              if ($this->ru_s($RV, $this->ADJECTIVE, '')) {
                  $this->ru_s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->ru_s($RV, $this->VERB, ''))
                      $this->ru_s($RV, $this->NOUN, '');
              }
          }

          # Step 2
          $this->ru_s($RV, 'и$', '');

          # Step 3
          if ($this->ru_m($RV, $this->DERIVATIONAL))
              $this->ru_s($RV, 'ость?$', '');

          # Step 4
          if (!$this->ru_s($RV, 'ь$', '')) {
              $this->ru_s($RV, 'ейше?', '');
              $this->ru_s($RV, 'нн$', 'н'); 
          }

          $stem = $start.$RV;
        } while(false);

        $ru_cached_words[$word] = $stem;

        return $stem;
    }
    
    private function en_stem_word( $word )
    {
        static $en_cached_words = array();
        
        $word = strtolower($word);
        
        if(isset($en_cached_words[$word])) return $en_cached_words[$word];

        // Strip punctuation, etc. Keep ' and . for URLs and contractions.
        if ( substr($word, -2) == "'s" ) {
            $word = substr($word, 0, -2);
        }
        $word = preg_replace("/[^a-z0-9'.-]/", '', $word);

        $first = '';
        if ( strpos($word, '-') !== false ) {
            //list($first, $word) = explode('-', $word);
            //$first .= '-';
            $first = substr($word, 0, strrpos($word, '-') + 1); // Grabs hyphen too
            $word = substr($word, strrpos($word, '-') + 1);
        }
        if ( strlen($word) > 2 ) {
            $word = $this->en_step_1($word);
            $word = $this->en_step_2($word);
            $word = $this->en_step_3($word);
            $word = $this->en_step_4($word);
            $word = $this->en_step_5($word);
        }

        $stem = $first . $word;
        $en_cached_words[$word] = $stem;
        
        return $stem;
    }
    
    public function Stem($Word)
    {
        if(preg_match('/^[a-z]+$/', $Word)) return $this->en_stem_word ($Word);
        return $this->ru_stem_word ($Word);
    }

    /**
     *  Performs the functions of steps 1a and 1b of the Porter Stemming Algorithm.
     *
     *  First, if the word is in plural form, it is reduced to singular form.
     *  Then, any -ed or -ing endings are removed as appropriate, and finally,
     *  words ending in "y" with a vowel in the stem have the "y" changed to "i".
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    private function en_step_1( $word )
    {
		// Step 1a
		if ( substr($word, -1) == 's' ) {
            if ( substr($word, -4) == 'sses' ) {
                $word = substr($word, 0, -2);
            } elseif ( substr($word, -3) == 'ies' ) {
                $word = substr($word, 0, -2);
            } elseif ( substr($word, -2, 1) != 's' ) {
                // If second-to-last character is not "s"
                $word = substr($word, 0, -1);
            }
        }
		// Step 1b
        if ( substr($word, -3) == 'eed' ) {
			if ($this->en_count_vc(substr($word, 0, -3)) > 0 ) {
	            // Convert '-eed' to '-ee'
	            $word = substr($word, 0, -1);
			}
        } else {
            if ( preg_match('/([aeiou]|[^aeiou]y).*(ed|ing)$/', $word) ) { // vowel in stem
                // Strip '-ed' or '-ing'
                if ( substr($word, -2) == 'ed' ) {
                    $word = substr($word, 0, -2);
                } else {
                    $word = substr($word, 0, -3);
                }
                if ( substr($word, -2) == 'at' || substr($word, -2) == 'bl' ||
                     substr($word, -2) == 'iz' ) {
                    $word .= 'e';
                } else {
                    $last_char = substr($word, -1, 1);
                    $next_to_last = substr($word, -2, 1);
                    // Strip ending double consonants to single, unless "l", "s" or "z"
                    if ( $this->en_is_consonant($word, -1) &&
                         $last_char == $next_to_last &&
                         $last_char != 'l' && $last_char != 's' && $last_char != 'z' ) {
                        $word = substr($word, 0, -1);
                    } else {
                        // If VC, and cvc (but not w,x,y at end)
                        if ( $this->en_count_vc($word) == 1 && $this->en_o($word) ) {
                            $word .= 'e';
                        }
                    }
                }
            }
        }
        // Step 1c
        // Turn y into i when another vowel in stem
        if ( preg_match('/([aeiou]|[^aeiou]y).*y$/', $word) ) { // vowel in stem
            $word = substr($word, 0, -1) . 'i';
        }
        return $word;
    }

    /**
     *  Performs the function of step 2 of the Porter Stemming Algorithm.
     *
     *  Step 2 maps double suffixes to single ones when the second-to-last character
     *  matches the given letters. So "-ization" (which is "-ize" plus "-ation"
     *  becomes "-ize". Mapping to a single character occurence speeds up the script
     *  by reducing the number of possible string searches.
     *
     *  Note: for this step (and steps 3 and 4), the algorithm requires that if
     *  a suffix match is found (checks longest first), then the step ends, regardless
     *  if a replacement occurred. Some (or many) implementations simply keep
     *  searching though a list of suffixes, even if one is found.
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    private function en_step_2( $word )
    {
        switch ( substr($word, -2, 1) ) {
            case 'a':
                if ( $this->en_replace($word, 'ational', 'ate', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'tional', 'tion', 0) ) {
                    return $word;
                }
                break;
            case 'c':
                if ( $this->en_replace($word, 'enci', 'ence', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'anci', 'ance', 0) ) {
                    return $word;
                }
                break;
            case 'e':
                if ( $this->en_replace($word, 'izer', 'ize', 0) ) {
                    return $word;
                }
                break;
            case 'l':
                // This condition is a departure from the original algorithm;
                // I adapted it from the departure in the ANSI-C version.
				if ( $this->en_replace($word, 'bli', 'ble', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'alli', 'al', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'entli', 'ent', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'eli', 'e', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ousli', 'ous', 0) ) {
                    return $word;
                }
                break;
            case 'o':
                if ( $this->en_replace($word, 'ization', 'ize', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'isation', 'ize', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ation', 'ate', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ator', 'ate', 0) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->en_replace($word, 'alism', 'al', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'iveness', 'ive', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'fulness', 'ful', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ousness', 'ous', 0) ) {
                    return $word;
                }
                break;
            case 't':
                if ( $this->en_replace($word, 'aliti', 'al', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'iviti', 'ive', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'biliti', 'ble', 0) ) {
                    return $word;
                }
                break;
            case 'g':
                // This condition is a departure from the original algorithm;
                // I adapted it from the departure in the ANSI-C version.
                if ( $this->en_replace($word, 'logi', 'log', 0) ) { //*****
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 3 of the Porter Stemming Algorithm.
     *
     *  Step 3 works in a similar stragegy to step 2, though checking the
     *  last character.
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    private function en_step_3( $word )
    {
        switch ( substr($word, -1) ) {
            case 'e':
                if ( $this->en_replace($word, 'icate', 'ic', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ative', '', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'alize', 'al', 0) ) {
                    return $word;
                }
                break;
            case 'i':
                if ( $this->en_replace($word, 'iciti', 'ic', 0) ) {
                    return $word;
                }
                break;
            case 'l':
                if ( $this->en_replace($word, 'ical', 'ic', 0) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ful', '', 0) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->en_replace($word, 'ness', '', 0) ) {
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 4 of the Porter Stemming Algorithm.
     *
     *  Step 4 works similarly to steps 3 and 2, above, though it removes
     *  the endings in the context of VCVC (vowel-consonant-vowel-consonant
     *  combinations).
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    private function en_step_4( $word )
    {
        switch ( substr($word, -2, 1) ) {
            case 'a':
                if ( $this->en_replace($word, 'al', '', 1) ) {
                    return $word;
                }
                break;
            case 'c':
                if ( $this->en_replace($word, 'ance', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ence', '', 1) ) {
                    return $word;
                }
                break;
            case 'e':
                if ( $this->en_replace($word, 'er', '', 1) ) {
                    return $word;
                }
                break;
            case 'i':
                if ( $this->en_replace($word, 'ic', '', 1) ) {
                    return $word;
                }
                break;
            case 'l':
                if ( $this->en_replace($word, 'able', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ible', '', 1) ) {
                    return $word;
                }
                break;
            case 'n':
                if ( $this->en_replace($word, 'ant', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ement', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ment', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'ent', '', 1) ) {
                    return $word;
                }
                break;
            case 'o':
                // special cases
                if ( substr($word, -4) == 'sion' || substr($word, -4) == 'tion' ) {
                    if ( $this->en_replace($word, 'ion', '', 1) ) {
                        return $word;
                    }
                }
                if ( $this->en_replace($word, 'ou', '', 1) ) {
                    return $word;
                }
                break;
            case 's':
                if ( $this->en_replace($word, 'ism', '', 1) ) {
                    return $word;
                }
                break;
            case 't':
                if ( $this->en_replace($word, 'ate', '', 1) ) {
                    return $word;
                }
                if ( $this->en_replace($word, 'iti', '', 1) ) {
                    return $word;
                }
                break;
            case 'u':
                if ( $this->en_replace($word, 'ous', '', 1) ) {
                    return $word;
                }
                break;
            case 'v':
                if ( $this->en_replace($word, 'ive', '', 1) ) {
                    return $word;
                }
                break;
            case 'z':
                if ( $this->en_replace($word, 'ize', '', 1) ) {
                    return $word;
                }
                break;
        }
        return $word;
    }

    /**
     *  Performs the function of step 5 of the Porter Stemming Algorithm.
     *
     *  Step 5 removes a final "-e" and changes "-ll" to "-l" in the context
     *  of VCVC (vowel-consonant-vowel-consonant combinations).
     *
     *  @param string $word Word to reduce
     *  @access private
     *  @return string Reduced word
     */
    private function en_step_5( $word )
    {
        if ( substr($word, -1) == 'e' ) {
            $short = substr($word, 0, -1);
            // Only remove in vcvc context...
            if ( $this->en_count_vc($short) > 1 ) {
                $word = $short;
            } elseif ( $this->en_count_vc($short) == 1 && !$this->en_o($short) ) {
                $word = $short;
            }
        }
        if ( substr($word, -2) == 'll' ) {
            // Only remove in vcvc context...
            if ( $this->en_count_vc($word) > 1 ) {
                $word = substr($word, 0, -1);
            }
        }
        return $word;
    }

    /**
     *  Checks that the specified letter (position) in the word is a consonant.
     *
     *  Handy check adapted from the ANSI C program. Regular vowels always return
     *  FALSE, while "y" is a special case: if the prececing character is a vowel,
     *  "y" is a consonant, otherwise it's a vowel.
     *
     *  And, if checking "y" in the first position and the word starts with "yy",
     *  return true even though it's not a legitimate word (it crashes otherwise).
     *
     *  @param string $word Word to check
     *  @param integer $pos Position in the string to check
     *  @access public
     *  @return boolean
     */
    private function en_is_consonant( $word, $pos )
    {
        // Sanity checking $pos
        if ( abs($pos) > strlen($word) ) {
            if ( $pos < 0 ) {
                // Points "too far back" in the string. Set it to beginning.
                $pos = 0;
            } else {
                // Points "too far forward." Set it to end.
                $pos = -1;
            }
        }
        $char = substr($word, $pos, 1);
        switch ( $char ) {
            case 'a':
            case 'e':
            case 'i':
            case 'o':
            case 'u':
                return false;
            case 'y':
                if ( $pos == 0 || strlen($word) == -$pos ) {
                    // Check second letter of word.
                    // If word starts with "yy", return true.
                    if ( substr($word, 1, 1) == 'y' ) {
                        return true;
                    }
                    return !($this->en_is_consonant($word, 1));
                } else {
                    return !($this->en_is_consonant($word, $pos - 1));
                }
            default:
                return true;
        }
    }

    /**
     *  @ignore
     */
    private function en_count_vc( $word )
    {
        $m = 0;
        $length = strlen($word);
        $prev_c = false;
        for ( $i = 0; $i < $length; $i++ ) {
            $is_c = $this->en_is_consonant($word, $i);
            if ( $is_c ) {
                if ( $m > 0 && !$prev_c ) {
                    $m += 0.5;
                }
            } else {
                if ( $prev_c || $m == 0 ) {
                    $m += 0.5;
                }
            }
            $prev_c = $is_c;
        }
        $m = floor($m);
        return $m;
    }

    /**
     *  @ignore
     */
    private function en_o( $word )
    {
        if ( strlen($word) >= 3 ) {
            if ( $this->en_is_consonant($word, -1) && !$this->en_is_consonant($word, -2) &&
                 $this->en_is_consonant($word, -3) ) {
		        $last_char = substr($word, -1);
		        if ( $last_char == 'w' || $last_char == 'x' || $last_char == 'y' ) {
		            return false;
		        }
                return true;
            }
        }
        return false;
    }

    /**
     *  @ignore
     */
    private function en_replace( &$word, $suffix, $replace, $m = 0 )
    {
        $sl = strlen($suffix);
        if ( substr($word, -$sl) == $suffix ) {
            $short = substr_replace($word, '', -$sl);
            if ( $this->en_count_vc($short) > $m ) {
                $word = $short . $replace;
            }
            // Found this suffix, doesn't matter if replacement succeeded
            return true;
        }
        return false;
    }
}