@extends('userInvitation.layoutUserInvitation')


@section('content')

@if($isErr)
    <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
        <img src="http://cinehost.com/wp-content/uploads/2014/11/section_3_img_11.svg">
        <div class="step_block"></div>
        <span class="title">Invalid invitation</span>
    </div>
    @else
    <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
        <img src="http://cinehost.com/wp-content/uploads/2014/11/section_3_img_11.svg">
        <div class="step_block"></div>
        <span class="title">Please Sign Up to Create Your Account</span>
    </div>


    <form class="form-horizontal" method="post" action="/userInvitation/register" id="RegistrationForm" >
        {{csrf_field()}}
        <fieldset>

            <div class="col-lg-2 col-xs-2 col-md-2 col-sm-2 hidden-xs ac" style="clear:both">&nbsp;</div>
            <div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
                <div class="control-group success">
                    <label name="name">Name</label>
                    <div class="controls">
                        <input  id="name" name="lname" placeholder="" type="text">
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
                <div class="control-group success">
                    <label name="surname">Surname</label>
                    <div class="controls">
                        <input  id="surname" name="lsurname" placeholder="" type="text">
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-xs-2 col-md-2 col-sm-2 hidden-xs">&nbsp;</div>



            <div class="col-lg-2 col-xs-2 col-md-2 col-sm-2 hidden-xs ac" style="clear:both">&nbsp;</div>
            <div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
                <div class="control-group success">
                    <label name="pwd">Password</label>
                    <div class="controls">
                        <input  id="password" name="pwd" placeholder="" type="password">
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-xs-12 col-md-4 col-sm-4">
                <div class="control-group success">
                    <label name="password2">Confirm Password</label>
                    <div class="controls">
                        <input  id="password2" name="password2" placeholder="" type="password">
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xs-12 col-md-12 col-sm-12">
                <input type="hidden" name="tk" value="{{$tk}}" />
                <button class="btn cq-sign-in-submit dark" id="reg-submit">SUBMIT</button>
            </div>
        </fieldset>
    <script>
        $(document).ready(function(){

            $(".pop-up-box-body-fix").mCustomScrollbar({
                theme:"light-thick",
                advanced:{updateOnContentResize:true},
                mouseWheelPixels:300
            });

            $(".cq-sign-in-submit").click(function(){
                $('#name').css('border','1px solid #babbcb');
                $('#surname').css('border','1px solid #babbcb');
                $('#password').css('border','1px solid #babbcb');
                $('#password2').css('border','1px solid #babbcb');
                var nm = $('#name').val();
                var sm = $('#surname').val();
                var pass = $('#password').val();
                var pass2 = $('#password2').val();
                var isErr = false;
                if(nm==''){
                    $('#name').css('border','1px solid red');
                    isErr = true;
                }
                if(sm==''){
                    $('#surname').css('border','1px solid red');
                    isErr = true;
                }
                if(pass==''){
                    $('#password').css('border','1px solid red');
                    isErr = true;
                }
                if(pass2==''){
                    $('#password2').css('border','1px solid red');
                    isErr = true;
                }
                if(pass!=pass2){
                    $('#password').css('border','1px solid red');
                    $('#password2').css('border','1px solid red');
                    isErr = true;
                }

                if (isErr){
                    return false;
                }
                else{
                    $( "#RegistrationForm" ).submit();
                }
            });

        });
    </script>
    @endif
@stop