@extends('layouts.master')

<style>
	.success {
    text-align: center;
    margin-top: 128px;
}
	.success img {
    margin-bottom: 10px;
}
.success h1 {
    font-weight: 700;
    letter-spacing: 3px;
    font-size: 40px;
    color: #5c5c5c;
}
body, html {
    height: unset!important;
}
.success h5 {
    font-size: 17px;
    text-transform: capitalize;
}
a.conbutton {
    display: block;
    margin-bottom: 8px;
    padding: 6px 10px;
    background: #2e1c70;
    width: 220px;
    color: #fff!important;
    margin-left: 50%;
    transform: translateX(-50%);
    text-transform: uppercase;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<form method="POST" enctype="multipart/form-data" action="{{ route('paymentfailure') }}">
</form>
<div class="success">
	<div class="container">
		<img src="/public/images/cross.png">
		<h1>Payment Failed!</h1>
        <p><?= $message; ?></p>
		<h5>Your Payment ID - <?= $mihpayid; ?></h5>
		
		<a href="<?= url('/');?>"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="16" height="16"><g><g>
	<g>
		<path d="M492,236H68.442l70.164-69.824c7.829-7.792,7.859-20.455,0.067-28.284c-7.792-7.83-20.456-7.859-28.285-0.068    l-104.504,104c-0.007,0.006-0.012,0.013-0.018,0.019c-7.809,7.792-7.834,20.496-0.002,28.314c0.007,0.006,0.012,0.013,0.018,0.019    l104.504,104c7.828,7.79,20.492,7.763,28.285-0.068c7.792-7.829,7.762-20.492-0.067-28.284L68.442,276H492    c11.046,0,20-8.954,20-20C512,244.954,503.046,236,492,236z" data-original="#000000" class="active-path" style="fill:#2E1C70" data-old_color="#000000"/>
	</g>
</g></g> </svg> back to home</a>
	</div>
</div>