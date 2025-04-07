<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
  <head>
    <title> </title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style type="text/css">
      #outlook a {
        padding: 0;
      }
      .ReadMsgBody {
        width: 100%;
      }
      .ExternalClass {
        width: 100%;
      }
      .ExternalClass * {
        line-height: 100%;
      }
      body {
        margin: 0;
        padding: 0;
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
      }
      table,
      td {
        border-collapse: collapse;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
      }
      img {
        border: 0;
        height: auto;
        line-height: 100%;
        outline: none;
        text-decoration: none;
        -ms-interpolation-mode: bicubic;
      }
      p {
        display: block;
        margin: 13px 0;
      }
	  .footer-social{
text-align:left;
padding:0;
margin:0;
margin-bottom:0px;
}
.footer-social li{
list-style:none;
display:inline-block;
padding-right: 7px;
margin-bottom:0px;
}
.footer-social li a {
color:#446d82;
font-size: 15px;
letter-spacing: 0.5px;
}

.footer-social li a i{
width: 40px;
padding: 10px;
border-radius:2px;
line-height: 10px;
background: #243140;
text-align: center;
color: #5f738a;
box-shadow: 0px 0px 10px 0px rgba(53, 67, 78,0.10);
}

.footer-social li a:hover i{
background:#8D2226;
color:#ffffff;
}
.light-footer .footer-social li a i {
    background: #e8ecf1;
}
    </style>
    <!--[if !mso]><!-->
    <style type="text/css">
      @media only screen and (max-width: 480px) {
        @-ms-viewport {
          width: 320px;
        }
        @viewport {
          width: 320px;
        }
      }
    </style>
    <style type="text/css">
      @media only screen and (min-width: 480px) {
        .dys-column-per-100 {
          width: 100% !important;
          max-width: 100%;
        }
      }
      @media only screen and (min-width: 480px) {
        .dys-column-per-100 {
          width: 100% !important;
          max-width: 100%;
        }
      }
      @media only screen and (max-width: 480px) {
        table.full-width-mobile {
          width: 100% !important;
        }
        td.full-width-mobile {
          width: auto !important;
        }
      }
      @media only screen and (min-width: 480px) {
        .dys-column-per-90 {
          width: 90% !important;
          max-width: 90%;
        }
      }
      @media only screen and (min-width: 480px) {
        .dys-column-per-90 {
          width: 90% !important;
          max-width: 90%;
        }
      }
      @media only screen and (min-width: 480px) {
        .dys-column-per-100 {
          width: 100% !important;
          max-width: 100%;
        }
      }
    </style>
  </head>
  <body>
    <div>

      <!-- HEADER EMAIL LOGO BORWITA -->
	  <div style="float:right;padding:10px 30px 10px 0">
		<img src="{{ @$param['logo'] }}" height="10" style="display: block; border: 0px;" />
	  </div>
     
      <!-- END HEADER EMAIL LOGO BORWITA -->

      @yield('body')

      <div style="background: #ffffff; background-color: #ffffff; margin: 0px auto; max-width: 600px;">
        <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background: #ffffff; background-color: #ffffff; width: 100%;">
          <tbody>
            <tr>
              <td style="direction: ltr; font-size: 0px; padding: 20px 0; text-align: center; vertical-align: top;">
                <div class="dys-column-per-100 outlook-group-fix" style="direction: ltr; display: inline-block; font-size: 13px; text-align: center; vertical-align: top; width: 100%;">
                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align: top;" width="100%">
                    <tr>
                      <td align="center" style="font-size: 0px; padding: 10px 25px; word-break: break-word;">
                        <div style="color: #777777; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px; line-height: 21px; text-align: center;font-weight:bold;">
						Follow Us :
								<ul class="footer-social" style="text-align: center;">									
									<li><a href="https://instagram.com/borwita.citraprima?igshid=19l72jqu3fvzl" target="_blank"><img src="data:image/png;base64,<?php echo base64_encode(file_get_contents(asset('project/public/icon/instagram.png'))); ?>"/></a></li>
									<li><a href="https://www.linkedin.com/company/pt-borwita-citra-prima" target="_blank"><img src="data:image/png;base64,<?php echo base64_encode(file_get_contents(asset('project/public/icon/linkedin.png'))); ?>"/></a></li>
									<li><a href="https://www.youtube.com/channel/UCAAa_AQClf9pJz5QtkF665A" target="_blank"><img src="data:image/png;base64,<?php echo base64_encode(file_get_contents(asset('project/public/icon/youtube.png'))); ?>"/></a></li>
								</ul>
								<br>
                          BORWITA
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>
