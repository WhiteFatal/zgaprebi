<?php
if(!defined('FILE_SECURITY_KEY')) die();
if(!has::role('structure')) die();

$_GET['sp'] = (isset($_GET['sp'])) ? $_GET['sp'] : 'sitemap';
?>

   <!-- Secondary nav -->
    <div class="secNav">
        <div class="secWrapper">
            <div class="secTop">
                <div class="balance">
                    <div class="balInfo"><?php _e('სტრუქტურა'); ?><span>&nbsp;</span></div>
                </div>
          </div>
            <div class="divider" style="margin:0 0 50px 0"><span></span></div>            
          <!-- Tabs container -->
            <div id="tab-container" class="tab-container">
              <div id="general">
              <center>
              	<?php echo adminlangchooser(); ?><br><br>
              </center>
                    <ul class="subNav">
                        <li><a href="?p=structure&sp=sitemap" <?php echo (($_GET['sp']=='sitemap')?'class="this"':''); ?> title=""><span class="icos-info"></span><?php _e('ვებ გვერდის სტრუქტურა'); ?></a></li>
                    </ul>
                </div>
                
          </div>
            <div class="divider"><span></span></div>
      </div> 
       <div class="clear"></div>
   </div>
</div>
<!-- Sidebar ends -->


<?php
include('structure/'.$_GET['sp'].'.php');
?>