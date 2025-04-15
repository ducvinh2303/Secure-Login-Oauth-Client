document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('select-display-type');
    const table = document.getElementById('table-display');
    const list = document.getElementById('list-display');
    const json = document.getElementById('json-display');

    table.style.display = 'flex'
    list.style.display = 'none'
    json.style.display = 'none'

    select.addEventListener('change', function() {
        const value = select.value;

        table.style.display = 'none'
        list.style.display = 'none'
        json.style.display = 'none'

        if (value === 'table') {
            table.style.display = 'flex'
        } else if (value === 'ulli') {
            list.style.display = 'flex'
        } else if (value === 'json') {
            json.style.display = 'flex'
        }
    });
});
