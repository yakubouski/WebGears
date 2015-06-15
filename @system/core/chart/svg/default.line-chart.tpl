<?
$Serie = [800,1900,500,100,4000,600,145,290,467,467];
$Max = max($Serie);
$Min = min($Serie);
$tMinY = round($Min - $Min*10/( ($Max)*10/$Height));
$tAvg = round(($Max - $Min)/2);
$X = -10;
$Y = $Height;
$dX = intval(($Width+20) / count($Serie));
?>
<svg:image width="$Width" width-scale='100%' height="$Height" class='line-chart'>
    <defs>
	<?svg::hgradient('serie-1', 'rgba(255,0,0,0.5)')?>
    </defs>
    <g width="100%">
    <?
	svg::line(0, $Height, $Width, $Height, '#D0D0D0');
	svg::text(0, $Height-4, $tMinY,'#505050',11);
	svg::line(0, $Height-Math::Percent($Max, $Max, $Height), $Width, $Height - Math::Percent($Max, $Max, $Height), '#D0D0D0');
	svg::text(0, $Height-Math::Percent($Max, $Max, $Height)+11, $Max,'#505050',11);
	svg::line(0, $Height-Math::Percent($tAvg, $Max, $Height), $Width, $Height - Math::Percent($tAvg, $Max, $Height), '#D0D0D0');
	svg::text(0, $Height-Math::Percent($tAvg, $Max, $Height)+11, $tAvg,'#505050',11);
    ?>
    </g>
    <g class='serie-1'>
    <?$pnts = ['m',[$X,$Y],'L',[$X,Math::Percent($Serie[0], $Max, $Height-10)]];foreach ($Serie as $v){
	$pnts[] = 'L';
	$pnts[] = [$X+=$dX,Math::Percent($v, $Max, $Height-10)];
    }
    $pnts[] = 'L'; $pnts[] = [$X,$Y]; $pnts[] = 'z';
    svg::path($pnts, 'url(#serie-1)', 'rgba(255,0,0,0.5)', 2);
    ?>
    </g>
</svg:image>