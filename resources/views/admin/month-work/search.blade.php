<ul>
  @forelse($data as $item)
  <li> <a href="#">{{ $item->employee_id }}</a> </li>
  @empty
  <li> <a style="color: red; font-size: 14px;">Data Not Found / Inactive Employee</a> </li>
  @endforelse
  <!-- This page is used to search employee in salary project page  -->
</ul>
