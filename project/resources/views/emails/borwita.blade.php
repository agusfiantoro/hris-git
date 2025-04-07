@extends('emails.layout')

@section('body')

<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background: #f7f7f7; background-color: #f7f7f7; width: 100%;">
  <tbody>
    <tr>
      <td>
        <div style="margin: 0px auto; max-width: 600px;">
          <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%;">
            <tbody>
              <tr>
                <td style="direction: ltr; font-size: 0px; padding: 20px 0; text-align: center; vertical-align: top;">
                  <div class="dys-column-per-100 outlook-group-fix" style="direction: ltr; display: inline-block; font-size: 13px; text-align: left; vertical-align: top; width: 100%;">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align: top;" width="100%">
                      <tr>
                        <td align="center" style="font-size: 0px; word-break: break-word;">
                          <div style="color: #777777; font-family: Calibri, Helvetica neue, sans-serif; font-size: 32px; font-weight: 700; line-height: 37px; text-align: center;">
                            Welcome to Borwita
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
      </td>
    </tr>
  </tbody>
</table>

<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background: #f7f7f7; background-color: #f7f7f7; width: 100%;">
  <tbody>
    <tr>
      <td>
        <div style="margin: 0px auto; max-width: 600px;">
          <table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%;">
            <tbody>
              <tr>
                <td style="direction: ltr; font-size: 0px; padding: 0 0 20px 0; text-align: center; vertical-align: top;">
                  <div class="dys-column-per-90 outlook-group-fix" style="direction: ltr; display: inline-block; font-size: 13px; text-align: left; vertical-align: top; width: 100%;">
                    <table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
                      <tbody>
                        <tr>
                          <td style="background-color: #ffffff; border: 1px solid #ccc; padding: 45px 75px; vertical-align: top;">
                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
                              @if($param['content_image'] != '')
                              <tr>
                                <td align="center" style="font-size: 0px; padding: 10px 25px; word-break: break-word;">
                                  <img src="{{ $param['content_image'] }}" width="125" height="180" style="display: block; border: 0px;" />
                  
                                </td>
                              </tr>
                              @endif
                              <tr>
                                <td align="left">               
                                  <div style="color: #8D2226; font-family: Calibri, Helvetica neue, sans-serif; font-size: 18px; text-align: center;font-weight:bold;">
                                    <br>
                                    {{ $param['content_title'] }}
                                  </div>
                                  <div style="color: #777777; font-family: Calibri, Helvetica neue, sans-serif; font-size: 16px; text-align: center;">
                                      Please find below information regarding your account : 
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td align="left" style="font-size: 16px; padding: 10px 25px; word-break: break-word;">
                                    @component('mail::panel')
                                    <b> {{ $param['content_username'] }}</b>
                                    @endcomponent
                                </td>
                              </tr>
                              <tr>
                                <td align="center" style="font-size: 0px; padding: 10px 25px; word-break: break-word;" vertical-align="middle">
                                  <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse: separate; line-height: 100%;">
                                    <tr>
                                      <td align="center" bgcolor="#ff6f6f" role="presentation" style="background-color: #ff6f6f; border: none; border-radius: 5px; cursor: auto; padding: 10px 25px;" valign="middle">
                                        <a href="{{ $param['content_link'] }}" style='background:#ff6f6f;color:#ffffff;font-family:Calibri, Helvetica neue, sans-serif;font-size:14px;font-weight:400;line-height:21px;margin:0;text-decoration:none;text-transform:none;' target='_blank'>
                                            Click to New Password
                                          </a>
                                      </td>
                                    </tr>
                                  </table>
                                </td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </td>
    </tr>
  </tbody>
</table>

@endsection