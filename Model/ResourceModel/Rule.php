<?php
namespace Resolve\Resolve\Model\ResourceModel;

class Rule extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('resolve_resolve_rule', 'rule_id');
    }

    public function massChangeStatus($ids, $status)
    {
        $db = $this->getConnection();
        $ids = array_map('intval', $ids);
        $db->update($this->getMainTable(),
            array('is_active' => $status), 'rule_id IN(' . implode(',', $ids) . ') ');

        return true;
    }

    public function getAttributes()
    {
        $db = $this->getConnection();
        $tbl   = $this->getTable('resolve_resolve_attribute');

        $select = $db->select()->from($tbl, new \Zend_Db_Expr('DISTINCT code'));
        return $db->fetchCol($select);
    }

    public function saveAttributes($id, $attributes)
    {
        $db = $this->getConnection();
        $tbl   = $this->getTable('resolve_resolve_attribute');

        $db->delete($tbl, array('rule_id=?' => $id));

        $data = array();
        foreach ($attributes as $code){
            $data[] = array(
                'rule_id' => $id,
                'code'    => $code,
            );
        }
        $db->insertMultiple($tbl, $data);

        return $this;
    }
}