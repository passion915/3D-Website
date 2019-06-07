KindEditor.plugin('myImg', function(K) {
        var editor = this, name = 'myImg';
        // 点击图标时执行
        editor.clickToolbar(name, function() {
                editor.insertHtml('你好');
        });
});