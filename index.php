<?php session_start(); ?>
<!DOCTYPE html>
<html lang="zh-tw">
    <head>
        <title>Message</title>
        <?php
        require_once dirname(__FILE__) . '/head.php';
        ?>

    </head>
    <body>
        <div class="container-fluid">

            <?php
            require_once dirname(__FILE__) . '/Message.php';
            $Message = new Message();
            $Data = $Message->index();
            ?>

            <div class="row">

                <div class="col-md-8">

                    <div class="row">
                        <div class="col-md-12">
                            <?php
                            if (!empty($Data['list']))
                                foreach ($Data['list'] as $k => $v) {
                                    ?>

                                    <div class="card mt-4">
                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-10">
                                                    <h5 class="card-title">
                                                        <?php echo nl2br(urldecode($v['message_content'])); ?>
                                                    </h5>
                                                    <p class="card-text">
                                                        <?php echo $v['message_person'] ? urldecode($v['message_person']) : 'Guest' ?>
                                                        &nbsp;／&nbsp;
                                                        <?php echo $v['message_time']; ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-2">
                                                    <form method="post" action="./Message.php"
                                                          id="FormDelete_<?php echo $v['id']; ?>">
                                                        <input type="hidden" name="method" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $v['id']; ?>">
                                                    </form>
                                                    <button class="btn btn-danger" style="display: inline-block"
                                                            onclick="deleteMessage('<?php echo $v['id']; ?>')">
                                                        刪除
                                                    </button>
                                                    <button class="btn btn-info" style="display: inline-block"
                                                            onclick="javascript:location.href='./edit.php?id=<?php echo $v['id']; ?>'">
                                                        修改
                                                    </button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                }
                            ?>
                        </div>
                        <div class="col-md-12 mt-4">
                            <nav aria-label="navigation">
                                <ul class="pagination">

                                    <?php
                                    $page_max = (isset($Data['limit']['page_max'])) ? $Data['limit']['page_max'] : 1;
                                    $page_now = (isset($Data['limit']['page_now'])) ? $Data['limit']['page_now'] : 1;
                                    for ($i = 1; $i <= $page_max; $i++) {
                                        ?>

                                        <li class="page-item<?php if ($i == $page_now)
                                            echo ' active'; ?>">
                                            <a class="page-link" href="./?page=<?php echo $i; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>

                                        <?php
                                    }
                                    ?>

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <form method="post" action="./Message.php">
                        <input type="hidden" name="method" value="add">
                        <input class="form-control mt-4" type="text" name="person" placeholder="姓名">
                        <textarea class="form-control mt-4" name="content" placeholder="留言"
                                  style="height: 30vh"></textarea>
                        <button class="btn btn-success mt-4">送出</button>
                    </form>
                </div>

            </div>

        </div>

        <?php require_once dirname(__FILE__) . '/footer.php'; ?>

    </body>
</html>
