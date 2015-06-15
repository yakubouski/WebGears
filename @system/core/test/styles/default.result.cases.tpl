<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
	<?$globalErrorsLog = []; set_error_handler (function($errno , $errstr, $errfile, $errline, $errcontext) use(&$globalErrorsLog) {
	    $globalErrorsLog[] = ['errno'=>$errno , 'errstr'=>$errstr, 'errfile'=>$errfile, 'errline'=>$errline];
	}, E_ALL | E_STRICT )?>
	<table width="80%">
	<?foreach (glob($PhpTestsSourcesMask) as $TestInclude) { 
	    try {
		ob_start();
		include ($TestInclude);
		ob_end_clean();
	    }  catch (Exception $e){
		$globalErrorsLog[] = ['errno'=>$e->getCode() , 'errstr'=>$e->getMessage(), 'errfile'=>$e->getFile(), 'errline'=>$e->getLine()];
	    }
	    foreach ($this->Sections as $n=>$s){?>
	    <tbody>
		<tr>
		    <td><?=$n+1?></td>
		    <td><?=$s['Package']?></td>
		    <td><?=$s['Name'] . ' ('.$s['File'].')'?></td>
		</tr>
		<tr>
		    <td></td>
		    <td colspan="2">
			<table width="100%">
			    <?foreach ($s['Tests'] as $i=>$t){?>
			    <tr>
				<td><?=$i+1?></td>
				<td>
				   <?=$t['Name']?> 
				    <div>
					<?=json_encode($t['Data'],JSON_UNESCAPED_UNICODE)?>
				    </div>
				</td>
				<td>
				    <?=json_encode($t['True'],JSON_UNESCAPED_UNICODE)?>
				</td>
				<td>
				    <?=json_encode($t['Result'],JSON_UNESCAPED_UNICODE)?>
				</td>
				<td>
				    <?=($t['ResultAssert']?'TRUE':'FALSE')?>
				</td>
			    </tr>
			    <?}?>
			</table>
		    </td>
		</tr>
	    </tbody>
	    
	    <?} $globalErrorsLog = [];
	} restore_error_handler(); ?>	
	</table>
    </body>
</html>
