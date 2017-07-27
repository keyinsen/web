<?php

namespace App\Http\Controllers\Leader;

use App\Models\Notice;
use App\Services\ApiResponseService;
use App\Http\Controllers\Controller;
use App\Repositories\NoticeRepository;
use App\Models\Structure\Organization;

use App\Http\Requests\Notice\ManageNoticeRequest;
use App\Http\Requests\Notice\UpdateNoticeRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class NoticeController.
 *
 * @author KYS
 * @modified zxl 2017-06-06
 */
class NoticeController extends Controller
{
	/**
	 * 公告仓库
	 * @var NoticeRepository
	 */
	private $notice;

	public function __construct(NoticeRepository $notice)
	{
		$this->notice = $notice;
	}

	/**
	 * 我的公告(右侧边栏“更多”按钮)
	 */
	public function more(MagageNoticeRequest $request)
	{
		$filter = $request->input('keyword', '');

		$organizations = $this->organization->findSelfAndAncestors();
		$organizations = $organizations->toArray();
		$organizationIds = array_column($organizations, 'id');

		$organizationsTree = $this->organization->traverse($organizations);

		$notices = $this->notice->page($request->input('page', 1), $request->input('perPage', 10), $filter, $organizationIds);
		$notices->appends($filter);

		return view('leader.notice.index')
			->withNotices($notices)
			->withFilter($filter)
			->withOrganizationTree($organizationsTree);
	}

	/**
	 * 公告管理
	 */
	public function index(ManageNoticeRequest $request)
	{
		$filter = $request->input('keyword', '');

		$organizations = $this->organization->findSelfAndAncestors();
		$organizations = $organizations->toArray();
		$organizationsTree = $this->organization->traverse($organizations);

		// 公告创建人
		$userId = userId();
		$notices = $this->notice->page($request->input('page', 1), $request->input('perPage', 10), $filter, $userId);
		$notices->appends($filter);

		return view('leader.notice.index')
			->withNotices($notices)
			->withFilter($filter)
			->withOrganizationTree($organizationsTree);
	}

	/**
	 * 撤销公告
	 */
	public function update(Notice $notice, UpdateNoticeRequest $request)
	{
		$res = $this->notice->update($notice, $request->all());

		if ($res) {
			return ApiResponseService::success();
		}

		return ApiResponseService::error(trans('exceptions.notice.updated_error'));
	}

	/**
	 * 公告详情
	 */
	public function show(Notice $notice, ManageNoticeRequest $request)
	{
		$id = $request->input('id', 0);

		$notice = $this->notice->find($id);

		return view('leader.notice.show')
			->withNotice($notice);
	}

	/*
    public function index(Request $request)
    {
        $notice = new Notice();
        $user=$notice->paginate(2);
        $Organization = new Organization();
        $Or=$Organization->all();
        return view('leader.notice.index')->with('user',$user)->with('Or',$Or);
    }

    public function delete($id,Request $request)
    {
        $deleted = DB::delete('delete from notice where id = :id', [':id' =>$id]);
        if($deleted == 1){
            return	response()->json([
                'staus'=>1,
                'delete'=>$deleted,
                'id'=>$id
            ]);
        }else{
            return	response()->json([
                'staus'=>-1,
                'delete'=>$deleted,
                'id'=>$id
            ]);
        }
    }
    public function search()
    {
       echo '此处执行通知查询';
  }

    public function add(Request $request)
    {
//        echo '此处执行添加公告';exit;
        $d=1;
        if($d == 1){
            return	response()->json([
                'staus'=>1,
                'd'=>$d
            ]);
        }else{
            return	response()->json([
                'staus'=>-1,
                'd'=>$d
            ]);
        }

    }

    public function details($q,Request $request)
    {
        $notice = new Notice();
        $user = $notice->where('id',$q)->get();
//        dump($user);
        $Organization = new Organization();
        $Or=$Organization->all();
        return view('leader.notice.details')->with('user',$user)->with('Or',$Or);

    }
	 */

}
