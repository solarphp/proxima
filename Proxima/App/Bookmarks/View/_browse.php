<?php
if (! $this->list) {
    echo $this->getText('ERR_NO_RECORDS');
} else {
    echo $this->partial('_list', $this->list);
    echo $this->pager($this->list->getPagerInfo());
    echo "<br />";
}
