<style>
    .sort-desc,
    .sort-asc {
        position: relative;
        padding-right: 25px;
    }

    .sort-asc:after,
    .sort-desc:after {
        position: absolute;
        top: 50%;
        margin-top: -11px;
        right: 5px;
        display: block;
        width: 0;
        height: 0;
        content: '';
        border: 5px solid transparent;
    }
    .sort-asc:after {
        border-bottom-color: black;
    }
    .sort-desc:after {
        margin-top: 1px;
        border-top-color: black;
    }
</style>

<h1>Record list</h1>

<table class="table table-bordered">
    <thead>
    <tr>
        {% for gridField, fieldTitle in gridFields %}
            <th><a href="/?field={{ gridField }}{% if field == gridField and sort == 'ASC' %}&sort=DESC{% endif %}"
                   class="{% if field == gridField %}{{ sort | lower == 'asc' ? 'sort-asc' : 'sort-desc' }}{% endif %}">{{ fieldTitle }}</a></th>
        {% endfor %}
    </tr>
    </thead>
    <tbody>

    {% for record in records %}
        <tr>
            <td><a href="/record/view/{{ record['id'] }}">{{ record['id'] }}</a></td>
            <td>{{ record['record_from'] }}</td>
            <td>{{ record['record_to'] }}</td>
            <td>{{ record['programs'] | join("<br />") }}</td>
        </tr>
    {% endfor %}

    {% if records | length == 0 %}
        <tr>
            <td colspan="4">No records found</td>
        </tr>
    {% endif %}
    </tbody>
</table>