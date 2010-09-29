<?php include_once '../inc/VERSION.inc'; ?>
    </ul> 
    <ul class="entry button" id="x-button-get-entry">
        <li>
        <?php if (strpos($_SERVER['PHP_SELF'], 'i/index.php')) { ?>
            <div class="x-button" align="center" onclick="getEntry();">载入更多</div>
        <?php } else { ?>
            <div align="center"><?php echo VWSCore::pagination(); ?></div>
        <?php } ?>
        </li>
    </ul>
</div>
<p id="copyright">Powered by Project VaynWords</p>
</body>
</html>
