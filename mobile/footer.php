<?php include_once '../inc/VERSION.inc'; ?>
    </ul> 
    <ul class="entry button" id="x-button-get-entry">
        <li>
        <?php if ($_SERVER['QUERY_STRING']) { ?>
            <div align="center"><?php echo VWSCore::pagination('/mobile/'); ?></div>
        <?php } else { ?>
            <div class="x-button" align="center" onclick="getEntry();">载入更多</div>
        <?php } ?>
        </li>
    </ul>
</div>
<p id="copyright">Powered by Project VaynWords</p>
</body>
</html>
