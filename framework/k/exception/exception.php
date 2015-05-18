<?php

defined( 'K_PATH' ) or die( 'DIRECT ACCESS IS NOT ALLOWED' );
/**
 * Exception class.
 *
 * @category Exceptions
 */
class K_Exception_Exception extends Exception {
    /**
     * @var  array  PHP error code => human readable name
     */
    public static $php_errors = array(
        E_ERROR => 'Fatal Error',
        E_USER_ERROR => 'User Error',
        E_PARSE => 'Parse Error',
        E_WARNING => 'Warning',
        E_USER_WARNING => 'User Warning',
        E_STRICT => 'Strict',
        E_NOTICE => 'Notice',
        E_RECOVERABLE_ERROR => 'Recoverable Error',
        );

    /**
     * @var  string  error view content type
     */
    public static $error_view_content_type = 'text/html';

    /**
     * 
     * @var  string  error rendering view
     */

    public static $error_view = 'error';


    /**
     * Creates a new translated exception.
     *
     *
     * @param   string          error message
     * @param   array           translation variables
     * @param   integer|string  the exception code
     * @return  void
     */
    public function __construct( $message, array $variables = null, $code = 0 ) {
        // Заменяем ключевые слова значениями в $message
        $message = empty( $variables ) ? $message : strtr( $message, $variables );

        // Pass the message and integer code to the parent
        parent::__construct( $message, ( int )$code );

        $this->code = $code;
    }

    /**
     * Magic object-to-string method.
     *
     *     echo $exception;
     *
     * @uses    Exception_Exception::text
     * @return  string
     */
    public function __toString() {
        return K_Exception::text( $this );
    }

    /**
     * Inline exception handler, displays the error message, source of the
     * exception, and the stack trace of the error.
     *
     * @uses    Exception_Exception::text
     * @param   object   exception object
     * @return  boolean
     */
    public static function handler( exception $e ) {
        try {
            // устанавливаем ошибку в контроллер, что бы не рендерил представление
            K_Controller::setError();

            // Get the exception information
            $type = get_class( $e );
            $code = $e->getCode();
            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();

            // Get the exception backtrace
            $trace = $e->getTrace();

            if ( $e instanceof ErrorException ) {
                if ( isset( K_Exception::$php_errors[$code] ) ) {
                    // Use the human-readable error name
                    $code = K_Exception::$php_errors[$code];
                }

            }

            // Create a text version of the exception
            $error = K_Exception::text( $e );

            if ( K_Request::isAjax() === true ) {
                // Just display the text of the exception
                echo "\n{$error}\n";
                // добовляем ошибку в логгер и дебагер
                K_Log::get()->add( $error );
                K_Debug::get()->add( $error, $trace );
                exit( 1 );
            }
            echo "\n{$error}\n";

            // добовляем ошибку в логгер и дебагер
            K_Log::get()->add( $error );
            K_Debug::get()->addError( $error, $trace );

            exit( 1 );
        }
        catch ( exception $e ) {
            // Clean the output buffer if one exists
            ob_get_level() and ob_clean();

            // Display the exception text
            echo K_Exception::text( $e ), "\n";

            // Exit with an error status
            exit( 1 );
        }
    }

    /**
     * Get a single line of text representing the exception:
     *
     * Error [ Code ]: Message ~ File [ Line ]
     *
     * @param   object  Exception
     * @return  string
     */
    public static function text( exception $e ) {
        return sprintf( 'Message: %s ERROR: %s Code:[ %s ] Fiel: %s ~ Line [ %d ]', strip_tags( $e->getMessage() ), get_class( $e ), $e->getCode(), $e->getFile(), $e->getLine() );    }

}
