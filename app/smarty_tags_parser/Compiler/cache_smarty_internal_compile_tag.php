<?php
    /**
     *  系统自动创建
     *  时间：2020/12/01 17:22:59
     */
    class Smarty_Internal_Compile_tag extends Smarty_Internal_CompileBase {
        //必要的属性
        public $required_attributes = array();
        //可传入的属性
        public $optional_attributes = array('_any');
        //?
        public $shorttag_order = [];
        public function compile($args, $compiler)
        {
            //获取内容，然后交给Smarty模板处理
            $_attr = $this->getAttributes($compiler, $args);
            $_attr = array_change_key_case($_attr,CASE_LOWER);//把数据的字符串键名全为小写，此方法默认小写，若大写请用: array_change_key_case($_attr, CASE_UPPER);
            $item = isset($_attr['item']) ? $_attr['item'] : "'tag'";
            $_attr['lib'] = isset($_attr['lib']) ? $_attr['lib'] : 'tag';
            $var = str_replace("'",'','tag_'.$item);
            $notClose = isset($_attr['item']) ? false : true;
            $compiler->notClose = $notClose;
            return $this->processOutput($compiler,$_attr,$item,$var,'tag');
        }
    }
    class Smarty_Internal_Compile_tagelse extends Smarty_Internal_CompileBase {
        public function compile($args, $compiler, $parameter)
        {
            $_attr = $this->getAttributes($compiler, $args);
            $notClose = isset($_attr['item']) ? false : true;
            $compiler->notClose = $notClose;
            list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('tag'), $notClose);
            $this->openTag($compiler, 'tagelse', array('tagelse', $nocache, $item, $key));
            return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
        }
    }
    class Smarty_Internal_Compile_tagclose extends Smarty_Internal_CompileBase {
        public function compile($args, $compiler, $parameter)
        {
            $_attr = $this->getAttributes($compiler, $args);
            if ($compiler->nocache) {
                $compiler->tag_nocache = true;
            }
            $notClose = isset($_attr['item']) ? false : true;
            $compiler->notClose = $notClose;
            list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('tag', 'tagelse'), $notClose);
            return "<?php } ?>";
        }
    }