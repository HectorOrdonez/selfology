<?php
/**
 * Project: The Mindcraft Project
 * User: Hector Ordonez
 * Description:
 * View chunk for the admin footer.
 * Date: 25/07/13 02:00
 *
 * @var \application\engine\View $this
 */
?>
</div>
<!-- Closing logged-in Content -->

<div id='footer'>
</div>

<div id='admin_footer'>
    <div id='adminFooterWrapper'>
        
        <div id="adminActions">
            <a class='admin_option usersManagement hoveringShadow' href="<?php echo _SYSTEM_BASE_URL; ?>usersManagement"></a>
            <a class='admin_option adminSettings hoveringShadow'></a>
            <a class='admin_option releaseHistory hoveringShadow' href="<?php echo _SYSTEM_BASE_URL; ?>releaseHistory"></a>
        </div>
        
        <div id="adminWelcome">
            <div class='' id='helloMe'>
                <span class='ftype_titleC'>Welcome </span><span class='ftype_titleC' data-field='username'><?php echo $this->getParameter('userName'); ?></span><span class='ftype_titleC'>!</span>
            </div>
            <div id='adminLogo'></div>
        </div>
    </div>
</div>
</body>
</html>