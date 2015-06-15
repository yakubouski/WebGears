<?php
class WidgetInfo extends \Html\Widget {

    private function icon($name) {
	?>
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: none;">
    <?
    $icon = 'icon-information';
    switch($name) {
	    case 'warning':
		$icon = 'icon-warning';
    ?>
    <symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-warning">
<path d="M87.194,77.145L53.031,17.974c-0.625-1.083-1.781-1.75-3.031-1.75s-2.406,0.667-3.031,1.75L12.636,77.439c-0.625,1.083-0.625,2.417,0,3.5s1.781,1.75,3.031,1.75h68.666c0.007,0,0.015,0.001,0.02,0c1.934,0,3.5-1.567,3.5-3.5C87.853,78.426,87.608,77.72,87.194,77.145z M21.729,75.689L50,26.724l28.271,48.966H21.729z M48.194,38.546h3.631c0.298,0,0.584,0.12,0.793,0.334c0.208,0.215,0.321,0.503,0.313,0.802l-0.73,22.912c-0.015,0.601-0.507,1.079-1.107,1.079h-2.168c-0.601,0-1.093-0.479-1.107-1.079l-0.73-22.912c-0.008-0.299,0.105-0.587,0.313-0.802C47.61,38.666,47.896,38.546,48.194,38.546zM52.885,66.714v4.148c0,0.611-0.495,1.107-1.107,1.107h-3.535c-0.612,0-1.107-0.496-1.107-1.107v-4.148c0-0.611,0.495-1.107,1.107-1.107h3.535C52.39,65.606,52.885,66.103,52.885,66.714z"/>
</symbol>
    <?
		break;
	    case 'lightbulb': case 'lamp':
		$icon = 'icon-lightbulb';
    ?>
<symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-lightbulb">
<path d="M50,11.276c-11.938,0-21.65,9.868-21.65,21.998c0,4.825,2.805,9.168,5.279,12.998c1.459,2.257,2.836,4.389,3.145,5.982c0.869,4.499,1.367,15.169,1.371,15.276c0.035,0.746,0.648,1.333,1.396,1.333h20.918c0.385,0,0.752-0.158,1.016-0.437c0.264-0.28,0.4-0.655,0.379-1.038c-0.26-4.676-0.08-12.968,1.508-15.315c0.275-0.406,0.592-0.856,0.934-1.348c2.752-3.925,7.354-10.498,7.354-17.452C71.648,21.145,61.938,11.276,50,11.276z M62.008,49.126c-0.354,0.503-0.676,0.966-0.959,1.383c-2.189,3.235-2.16,11.954-2.047,15.562H40.869c-0.164-3.063-0.627-10.579-1.354-14.345c-0.412-2.127-1.861-4.37-3.539-6.969c-2.266-3.507-4.832-7.48-4.832-11.482c0-10.59,8.459-19.204,18.855-19.204s18.855,8.614,18.855,19.204C68.855,39.348,64.568,45.469,62.008,49.126z M39.176,70.939h21.648v10.825c0,2.036-1.318,3.745-3.143,4.37c-0.338,1.481-1.656,2.589-3.24,2.589h-8.883c-1.584,0-2.902-1.107-3.24-2.589c-1.824-0.625-3.143-2.334-3.143-4.37V70.939z M46.961,18.1c0.152,0.531-0.154,1.085-0.686,1.236c-9.801,2.809-9.9,13.34-9.9,13.786c0,0.552-0.445,1.001-0.998,1.002h-0.002c-0.551,0-0.998-0.444-1-0.995c0-0.124,0.078-12.485,11.35-15.715C46.252,17.262,46.809,17.568,46.961,18.1z M64,38.32c-0.197,0.219-0.469,0.33-0.742,0.33c-0.238,0-0.479-0.085-0.67-0.257c-0.41-0.37-0.443-1.002-0.072-1.413c3.164-3.512,0.523-7.805,0.496-7.848c-0.295-0.468-0.154-1.085,0.313-1.379c0.467-0.295,1.084-0.154,1.379,0.313C65.918,29.995,67.336,34.62,64,38.32z"/>
</symbol>
    <?
		break;
	    case 'success':
		$icon = 'icon-success';
		?>
<symbol viewBox="0 0 24 24" catalog="material-design" id="icon-success">
<path d="M7.9,10.1l-1.4,1.4L11,16L21,6l-1.4-1.4L11,13.2L7.9,10.1z M20,12c0,4.4-3.6,8-8,8s-8-3.6-8-8s3.6-8,8-8c0.8,0,1.5,0.1,2.2,0.3l1.6-1.6C14.6,2.3,13.3,2,12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10H20z"/>
</symbol>
    <?
		break;
	    case 'information': case 'info':
		$icon = 'icon-information';
    ?>
    <symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-information">
<path d="M49.999,9.001C27.392,9.001,9.001,27.393,9.001,50s18.391,40.999,40.998,40.999s41-18.392,41-40.999S72.606,9.001,49.999,9.001z M49.999,86.999c-20.4,0-36.998-16.598-36.998-36.999s16.598-36.999,36.998-36.999c20.402,0,37,16.598,37,36.999S70.401,86.999,49.999,86.999z M61.794,72.349v5.428c0,0.228-0.186,0.411-0.41,0.411H41.458c-0.227,0-0.41-0.184-0.41-0.411v-5.428c0-0.227,0.184-0.411,0.41-0.411h5.74v-28.93h-5.799c-0.227,0-0.408-0.184-0.408-0.409v-5.551c0-0.228,0.182-0.41,0.408-0.41h13.596c0.227,0,0.41,0.183,0.41,0.41v34.89h5.979C61.608,71.938,61.794,72.122,61.794,72.349z M43.685,26.502c0-3.452,2.797-6.25,6.25-6.25c3.451,0,6.25,2.798,6.25,6.25s-2.799,6.25-6.25,6.25C46.481,32.752,43.685,29.954,43.685,26.502z"/>
</symbol>
    <?
		break;
	    case 'quotation': case 'quotes':
		$icon = 'icon-quotation';
    ?>
    <symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-quotation">
<path d="M25.566,47.766v6.256c0,0.849-0.688,1.537-1.537,1.537c-3.028,0-4.674,3.105-4.902,9.233h4.902c0.849,0,1.537,0.688,1.537,1.537v13.21c0,0.848-0.688,1.536-1.537,1.536H10.959c-0.849,0-1.537-0.688-1.537-1.536v-13.21c0-2.938,0.296-5.634,0.879-8.015c0.598-2.44,1.516-4.574,2.727-6.342c1.247-1.817,2.807-3.243,4.637-4.236c1.842-0.999,3.983-1.506,6.365-1.506C24.878,46.23,25.566,46.918,25.566,47.766z M45.381,46.23c-2.381,0-4.523,0.507-6.365,1.506c-1.83,0.993-3.39,2.419-4.637,4.236c-1.212,1.769-2.129,3.902-2.728,6.344c-0.583,2.38-0.878,5.076-0.878,8.013v13.21c0,0.848,0.688,1.536,1.536,1.536h13.071c0.848,0,1.536-0.688,1.536-1.536v-13.21c0-0.849-0.688-1.537-1.536-1.537h-4.833c0.225-6.128,1.848-9.233,4.833-9.233c0.848,0,1.536-0.688,1.536-1.537v-6.256C46.917,46.918,46.229,46.23,45.381,46.23zM89.041,18.925H75.97c-0.848,0-1.537,0.688-1.537,1.537v13.209c0,0.849,0.688,1.537,1.537,1.537h4.902c-0.228,6.128-1.874,9.232-4.902,9.232c-0.848,0-1.537,0.688-1.537,1.537v6.257c0,0.848,0.688,1.536,1.537,1.536c2.382,0,4.523-0.507,6.365-1.506c1.829-0.993,3.389-2.418,4.635-4.236c1.212-1.768,2.13-3.901,2.728-6.342c0.583-2.381,0.879-5.077,0.879-8.015V20.462C90.578,19.613,89.889,18.925,89.041,18.925z M67.69,18.925H54.619c-0.848,0-1.536,0.688-1.536,1.537v13.209c0,0.849,0.688,1.537,1.536,1.537h4.833c-0.225,6.128-1.848,9.232-4.833,9.232c-0.848,0-1.536,0.688-1.536,1.537v6.257c0,0.848,0.688,1.536,1.536,1.536c2.381,0,4.523-0.507,6.365-1.506c1.83-0.993,3.39-2.418,4.636-4.236c1.213-1.768,2.131-3.901,2.729-6.343c0.582-2.38,0.877-5.076,0.877-8.013V20.462C69.226,19.613,68.538,18.925,67.69,18.925z"/>
</symbol>
    <?
		break;
	    case 'stop':
		$icon = 'icon-stop';
    ?>
    <symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-stop">
<path d="M87.065,48.25L70.048,18.775c-0.625-1.083-1.781-1.75-3.031-1.75H32.983c-1.25,0-2.406,0.667-3.031,1.75L12.935,48.25c-0.625,1.083-0.625,2.417,0,3.5l17.017,29.475c0.625,1.083,1.781,1.75,3.031,1.75h34.034c1.25,0,2.406-0.667,3.031-1.75L87.065,51.75C87.69,50.667,87.69,49.333,87.065,48.25z M64.996,75.975H35.004L20.008,50l14.996-25.975h29.992L79.992,50L64.996,75.975z M35.326,52.977c0,2.437-1.875,4.506-5.865,4.506c-1.661,0-3.298-0.432-4.119-0.884l0.669-2.717c0.885,0.453,2.242,0.906,3.644,0.906c1.508,0,2.309-0.625,2.309-1.574c0-0.907-0.691-1.423-2.438-2.05c-2.415-0.842-3.989-2.178-3.989-4.291c0-2.479,2.071-4.377,5.499-4.377c1.639,0,2.847,0.346,3.709,0.733l-0.734,2.653c-0.581-0.281-1.617-0.691-3.04-0.691c-1.423,0-2.114,0.647-2.114,1.402c0,0.927,0.82,1.335,2.695,2.049C34.12,49.591,35.326,50.927,35.326,52.977z M36.453,42.733h11.171v2.759h-3.968v11.774h-3.3V45.492h-3.903V42.733z M55.156,42.496c-4.291,0-7.072,3.257-7.072,7.613c0,4.139,2.523,7.395,6.835,7.395c4.247,0,7.138-2.89,7.138-7.655C62.057,45.839,59.619,42.496,55.156,42.496z M55.091,54.895c-2.198,0-3.536-2.005-3.536-4.85c0-2.827,1.294-4.939,3.515-4.939c2.265,0,3.516,2.243,3.516,4.853C58.585,52.783,57.312,54.895,55.091,54.895z M64.22,57.267h3.256v-5.197c0.302,0.045,0.69,0.065,1.123,0.065c1.94,0,3.601-0.475,4.721-1.531c0.865-0.819,1.338-2.025,1.338-3.449c0-1.423-0.625-2.631-1.554-3.364c-0.969-0.776-2.414-1.165-4.44-1.165c-2.007,0-3.43,0.131-4.443,0.303V57.267z M67.476,45.234c0.237-0.064,0.69-0.129,1.358-0.129c1.639,0,2.566,0.799,2.566,2.134c0,1.489-1.078,2.372-2.825,2.372c-0.475,0-0.818-0.021-1.1-0.085V45.234z"/>
</symbol>
    <?
		break;
	    case 'question': case 'help':
		$icon = 'icon-question';
    ?>
    <symbol viewBox="0 0 100 100" catalog="basic-application" id="icon-question">
<path d="M51.857,9.001C29.25,9.001,10.858,27.393,10.858,50S29.25,90.999,51.857,90.999S92.856,72.607,92.856,50S74.465,9.001,51.857,9.001z M51.857,86.999c-20.401,0-36.999-16.598-36.999-36.999s16.598-36.999,36.999-36.999S88.856,29.599,88.856,50S72.259,86.999,51.857,86.999z M55.63,71.704v6.153c0,0.828-0.671,1.5-1.5,1.5h-5.314c-0.829,0-1.5-0.672-1.5-1.5v-6.153c0-0.828,0.671-1.5,1.5-1.5h5.314C54.959,70.204,55.63,70.876,55.63,71.704z M62.087,23.155c1.029,1.014,1.55,2.314,1.55,3.867v12.307c0,1.69-0.448,3.227-1.331,4.566l-6.885,10.496v11.159c0,0.828-0.671,1.5-1.5,1.5h-4.895c-0.829,0-1.5-0.672-1.5-1.5V55.098c0-1.835,0.427-3.399,1.271-4.65l6.981-10.42V28.942h-8.188v10.807c0,0.828-0.671,1.5-1.5,1.5h-4.86c-0.829,0-1.5-0.672-1.5-1.5V27.022c0-1.559,0.53-2.862,1.575-3.874c1.034-1,2.337-1.507,3.875-1.507h13.006C59.752,21.642,61.064,22.151,62.087,23.155z"/>
</symbol>
    <?
		break;
    }
    ?>
</svg>
<svg><use xlink:href="#<?=$icon?>"></use></svg>
<?
    }
    
    public function End($innerHtml) {
	$class = $this->arg('class');
	$style = $this->arg('style');
	$icon = $this->arg('icon');
?>
<div class="ui-info <?=$class?>" style="<?=$style?>">
    <?if(!empty($icon)){?><div class="icon"><?=$this->icon($icon)?></div><?}?><button class="button-close">&times;</button><?=$innerHtml?>
</div>
<?
    }

    public function Begin() {}
    public function Complete() { 
	$class = $this->arg('class');
	$style = $this->arg('style');
	$icon = $this->arg('icon');
?>
<div class="ui-info <?=$class?>" style="<?=$style?>">
    <?if(!empty($icon)){?><div class="icon"><?=$this->icon($icon)?></div><?}?>
    <button class="button-close">&times;</button>
    <?=$innerHtml?>
</div>
<?
    }
}