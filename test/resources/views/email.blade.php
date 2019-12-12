
<!-- <b>Notification</b>		:	{{$request['message']}}<br>
<b>From</b>				:	{{@$request['by']}}<br>
<b>To</b>				:	{{@$request['to']}}<br>
<b>Link</b>				:	<a href="http://toptechinfo.net/dms/public/{{$request['link']}}">{{$request['link']}}</a><br> -->
@php
$logo = (isset($request['logo']))?$request['logo']:'';
$address = (isset($request['address']))?$request['address']:'';

$link = (isset($request['link']))?$request['link']:'';

$message = (isset($request['message']))?$request['message']:'';
$title = (isset($request['title']))?$request['title']:'See the notifications ready and waiting just for you'; 

@endphp
<div style="margin:0px auto;padding:0px">
<img width="1" height="1" src="" style="margin:0px;padding:0px;display:inline-block;border:none;outline:none" class="CToWUd">

<table cellpadding="0" cellspacing="0" border="0" width="100%" bgcolor="#ffffff" class="m_1481346568675104311wrapper" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto;min-width:400px">
<tbody>
<tr>
<td class="m_1481346568675104311empty" width="100%" align="center" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">


<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" bgcolor="#ffffff" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>
<td class="m_1481346568675104311empty" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
<table cellpadding="0" cellspacing="0" border="0" width="496" align="center" class="m_1481346568675104311width_full" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">

<tbody>
<tr>
<td class="m_1481346568675104311empty m_1481346568675104311width_24" width="24" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
 </td>
<td class="m_1481346568675104311empty" align="center" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>
<td class="m_1481346568675104311empty" height="24" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
<tr>
<td style="padding:0px;margin:0px auto">
<table align="left" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>

<td class="m_1481346568675104311image m_1481346568675104311empty" align="left" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px;font-size:0px;line-height:100%;padding:0px"> @if($logo !='')	<a href="" style="text-decoration:none;border-style:none;border:0px;padding:0px;margin:0px" target="_blank"> <img src="http://toptechinfo.net/dms/public/images/logo/{{$logo}}" width="75" class="m_1481346568675104311avatar CToWUd" style="margin:0px;padding:0px;display:inline-block;border:none;outline:none;border-radius:16px"> </a> @endif</td>

</tr>
</tbody>
</table>
<table align="right" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>
<td class="m_1481346568675104311image m_1481346568675104311empty" align="right" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px;font-size:0px;line-height:100%;padding:0px"> <a href="" style="text-decoration:none;border-style:none;border:0px;padding:0px;margin:0px" target="_blank" > <img src="" width="32" style="margin:0px;padding:0px;display:inline-block;border:none;outline:none" class="CToWUd"> </a> </td>
</tr>
</tbody>
</table> </td>
</tr>
<tr>
<td class="m_1481346568675104311empty" height="12" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
@if($title !='')	
<tr>
<td class="m_1481346568675104311h1 m_1481346568675104311text_black" align="left" style="padding:0px;margin:0px auto;color:#292f33;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:32px;padding:0px;margin:0px;font-weight:bold;line-height:36px">{{$title}} </td>
</tr>
@endif
<tr>
<td class="m_1481346568675104311h1 m_1481346568675104311text_black" align="left" style="padding:0px;margin:0px auto;color:#292f33;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:25px;padding:0px;margin:0px;font-weight:bold;line-height:36px"> {{$message}} </td>
</tr>
<tr>
<td class="m_1481346568675104311empty" height="18" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>

<tr>
<td class="m_1481346568675104311empty" align="left" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
<table border="0" cellspacing="0" cellpadding="0" align="left" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>

<td align="center" class="m_1481346568675104311button" bgcolor="#00c0ef" style="padding:0px;margin:0px auto;border-radius:50px;line-height:18px"> 
@if($link !='')	
	<a href="{{$link}}" class="m_1481346568675104311button" style="text-decoration:none;border-style:none;border:0px;padding:0px;margin:0px;font-size:14px;font-family:'HelveticaNeue','Helvetica Neue',Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;border-radius:50px;padding:8px 18px;border:1px solid #00c0ef;display:inline-block;font-weight:bold" target="_blank" >
 Take a look
 </a> 
 @endif
</td>
</tr>
</tbody>
</table> </td>
</tr>
</tbody>
</table> </td>
<td class="m_1481346568675104311empty m_1481346568675104311width_24" width="24" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
 </td>
</tr>
</tbody>
</table> </td>
</tr>
</tbody>
</table>


<table cellpadding="0" cellspacing="0" border="0" align="center" width="472" class="m_1481346568675104311width_full" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>
<td class="m_1481346568675104311empty" width="12" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
<td class="m_1481346568675104311empty" align="center" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px">
<table cellpadding="0" cellspacing="0" border="0" align="center" width="100%" dir="ltr" style="padding:0px;line-height:1px;font-size:1px;margin:0px auto">
<tbody>
<tr>
<td class="m_1481346568675104311empty" height="72" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>

<tr>
<td class="m_1481346568675104311empty" height="12" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
<tr>
<td class="m_1481346568675104311empty" height="12" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
<tr>
<td class="m_1481346568675104311footer m_1481346568675104311deep_grey" align="center" style="padding:0px;margin:0px auto;color:#8899a6;font-family:'Helvetica Neue Light',Helvetica,Arial,sans-serif;font-size:12px;padding:0px;margin:0px;font-weight:normal;line-height:16px"> <span class="m_1481346568675104311addressLink">{{$address}}</span> </td>
</tr>
<tr>
<td class="m_1481346568675104311empty" height="72" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
</tbody>
</table> </td>
<td class="m_1481346568675104311empty" width="12" style="padding:0px;margin:0px auto;font-size:0px;line-height:1px;padding:0px"> &nbsp; </td>
</tr>
</tbody>
</table>

 </td>
</tr>
</tbody>
</table>
<section>
<div id="m_1481346568675104311new-gmail-hack" style="white-space:nowrap;font:15px courier;line-height:0;display:none">
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
</div>
</section><div class="yj6qo"></div><div class="adL">
</div></div>
