<?php
class AdminPageFramework_Minifiler_ProgressBuffer {
	
	public function __construct( $sTitle, $iTop=40, $iRight=5 ) {
		
		$this->sTitle = $sTitle;
		$this->iTop = $iTop;
		$this->iRight = $iRight;
		echo "<h3>{$sTitle}</h3>";
		
	}
	
	public function showText( $sString ) {
		
		static $iZdepth = 0;
		$sTop = $this->iTop . 'px';
		echo "<p style='width:100%;'><span style='position: absolute; z-index:{$iZdepth}; background:#fff; top: {$sTop}; width: 100%;'>"
			. $sString 
			. "</span></p>";
		$this->myFlush();
		$iZdepth++;
	}
		
	/**
	 * Output span with progress.
	 *
	 * @param $iCurrent integer Current progress out of total
	 * @param $iTotal   integer Total steps required to complete
	 */
	public function showProgress( $iCurrent, $iTotal ) {
		
		static $iZdepth = 0;
		$sTop = ( $this->iTop + 40 ) . 'px';
		echo "<p style='width:100%;'><span style='position: absolute; z-index:{$iZdepth};background-color:#FFF; top: {$sTop}; width: 100%;'>" 
				. round( $iCurrent / $iTotal * 100) 
			. "% </span></p>";
		$this->myFlush();
		$iZdepth++;
		
	}

	/**
	 * Flush output buffer
	 */
	static protected function myFlush() {
		
		echo( str_repeat( ' ', 256 ) );
		if ( @ob_get_contents() ) @ob_end_flush();
		flush();
		
	}
	
}