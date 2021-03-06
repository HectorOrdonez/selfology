<?php
/**
 * Project: The Mindcraft Project
 * User: Hector Ordonez
 * Description:
 * View chunk for the SignUp content.
 * Date: 22/11/13 20:00
 *
 * @var \application\engine\View $this
 */
?>
<?php $this->printChunk('header'); ?>

<div id='signUpBody'>
    <div id='signUpTitle'>
        <div class='ftype_contentB'>
            New in here?
            <p id='signUpNow'>
                SignUp now!
            </p>
        </div>
    </div>

    <div id='signUpBlock'>
        <form id='signUpForm' action="<?php echo _SYSTEM_BASE_URL; ?>signUp/signUp" method="POST">
            <label>
                <input id='signUpInputEmail' class='ftype_contentB shadowed' type='text' name='mail' placeholder="email address"/>
            </label>

            <div class='errorTextWrapper'>
                <p class='errorText' id='mailError'></p>
            </div>

            <label>
                <input id='signUpInputUsername' class='ftype_contentB shadowed' type='text' name='username' placeholder="username"/>
            </label>

            <div class='errorTextWrapper'>
                <p class='errorText' id='usernameError'></p>
            </div>

            <label>
                <input id='signUpInputPassword' class='ftype_contentB shadowed' type='password' name='password' placeholder="password"/>
            </label>

            <div class='errorTextWrapper'>
                <p class='errorText' id='passwordError'></p>
            </div>

            <div class='verticalSpace30'></div>

            <div id='signUpSubmitBox'>
                <div id='signUpSubmitText'>
                    <span class='ftype_titleA1'>Sign</span><span class='ftype_titleA2'>Up</span><span
                        class='ftype_titleA3'>!</span>
                </div>

                <div id='signUpImageBox'>
                    <div id='you'>
                        <img src='<?php echo _SYSTEM_BASE_URL . 'public/images/'; ?>SignUp1.png' alt='SignUp'/>
                    </div>
                    <div id='signUpped'>
                        <img src='<?php echo _SYSTEM_BASE_URL . 'public/images/'; ?>SignUp2.png' alt='SignUp'/>
                    </div>
                </div>
            </div>
        </form>

        <div>
            <div id='generalError'></div>
        </div>
    </div>
</div>

<div id='signUpConfirmation'>
    <p class='ftype_successA' id='confirmationHeader'>
        You have successfully become a MindCrafter!
    </p>
    <p class='ftype_contentB'>
        The MindCraft admins, Hector or Zuzanna, will have to confirm you as User before being able to log in.
    </p>
    <p class='ftype_contentB'>
        We'll send you a mail to your e-mail address when that is done!
    </p>
    
</div>

<?php $this->printChunk('footer'); ?>
