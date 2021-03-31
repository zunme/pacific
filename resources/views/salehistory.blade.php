            <div class="table-responsive">
              <table class="table table-bordered text-center" style="min-width: 500px">
                <thead>
                  <tr>
                    <th>날짜</th>
                    <th>분류</th>
                    <th>캐릭터명</th>
                    <th>상태</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ( $reservedata as $r_row)
                  <tr>
                    <td>{{$r_row->saledate}}</td>
                    <td><span class="text-danger">판매</span></td>
                    <td>{{$r_row->product_name}}</td>
                    <td>판매</td>
                  </tr>
                  @endforeach
                  @foreach ( $data as $row)
                  <tr>
                    <td>{{$row->updated_at->format('Y.m.d')}}</td>
                    <td><span class="text-danger">판매</span></td>
                    <td>{{$row->product_name}}</td>
                    <td>
                      @if( $row->trading_status=='CMPT')
                      판매완료
                      @else
                      {{ $types[ $row->trading_status ]}}
                      @endif
                      <a href="/my/salelist/detail/{{$row->trading_code}}" class="ml-2 btn btn-sm btn-secondary">상세</a>
                    </td>
                  </tr>
                  @endforeach
                  
                  @if ( count( $data) < 1 && count($reservedata) < 1 )
                  <tr>
                    <td colspan="4" > 판매내역이 없습니다.</td>
                  </tr>
                  @endif
                  <!--
                  <tr>
                    <td>2021.01.10</td>
                    <td><span class="text-danger">구매</span></td>
                    <td>태평양</td>
                    <td>구매신청</td>
                  </tr>
                  <tr>
                    <td>2021.01.10</td>
                    <td><span class="text-danger">구매</span></td>
                    <td>대서양</td>
                    <td>입금대기<a href="history_buy_detail.html" class="ml-2 btn btn-sm btn-secondary">상세</a></td>
                  </tr>
                  <tr>
                    <td>2021.01.10</td>
                    <td><span class="text-danger">구매</span></td>
                    <td>인도양</td>
                    <td>승인대기<a href="history_buy_detail.html" class="ml-2 btn btn-sm btn-secondary">상세</a></td>
                  </tr>
                  <tr>
                    <td>2021.01.10</td>
                    <td><span class="text-danger">구매</span></td>
                    <td>지중해</td>
                    <td>구매완료<a href="history_buy_detail.html" class="ml-2 btn btn-sm btn-secondary">상세</a></td>
                  </tr>
  -->
                </tbody>
              </table>
            </div>

            <nav>
              {{$data->links('layouts.pagination')}}  
            </nav>
