<?php 
namespace Topxia\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Topxia\Common\Paginator;
use Topxia\Service\Util\CCVideoClientFactory;

class CoursewareController extends BaseController
{
    public function manageAction(Request $request, $categoryId, $type)
    {
        $fields = $request->query->all();
        if(!empty($fields)){
            if($fields['method'] == 'tag'){
                $conditions = array(
                    'tagIds' => $fields['tagIds'],
                    'knowledgeId' => $fields['knowledgeId'],
                    'categoryId' => $categoryId
                );
            } else {
                $conditions = array(
                    'title' => $fields['title'],
                    'knowledgeId' => $fields['knowledgeId'],
                    'categoryId' => $categoryId
                );
            }
        } else {
            $conditions = array('categoryId' => $categoryId);
        }

        $count = $this->getCoursewareService()->searchCoursewaresCount($conditions);

        $paginator = new Paginator($this->get('request'), $count, 6);

        $coursewares = $this->getCoursewareService()->searchCoursewares(
            $conditions, 
            array('createdTime','desc'),
            $paginator->getOffsetCount(),  
            $paginator->getPerPageCount()
        );

        $category = $this->getCategoryService()->getCategory($categoryId);

        if ($type == 'list') {
            return $this->render('TopxiaAdminBundle:Courseware:list-view.html.twig',array(
                'category' => $category,
                'coursewares' => $coursewares
            ));
        } else {
            return $this->render('TopxiaAdminBundle:Courseware:thumb-view.html.twig',array(
                'category' => $category,
                'coursewares' => $coursewares
            ));
        }
    }

    public function createAction(Request $request, $categoryId)
    {
        $category = $this->getCategoryService()->getCategory($categoryId);

        if ($request->getMethod() == 'POST') {
            $courseware = $request->request->all();
            $factory = new CCVideoClientFactory();
            $client = $factory->createClient();
            $userIdAndVideoId = $this->getUserIdAndVideoId($courseware['url']);
            $videoInfo = $client->getVideoInfo($userIdAndVideoId['userId'],$userIdAndVideoId['videoId']);
            $videoInfo = json_decode($videoInfo);
            $courseware['title'] = $videoInfo->video->title;
            $courseware['image'] = $videoInfo->video->image;
            $courseware['releatedKnowledgeIds'] = array_filter(explode(',', $courseware['releatedKnowledgeIds']));
            $courseware['tagIds'] = array_filter(explode(',', $courseware['tagIds']));
            $courseware['categoryId'] = $categoryId;
            var_dump($courseware);exit();
            $this->getCoursewareService()->createCourseware($courseware);
        }

        return $this->render('TopxiaAdminBundle:Courseware:modal.html.twig',array(
            'category' => $category
        ));
    }

    private function getUserIdAndVideoId($url)
    {
        $query = parse_url($url);
        $querys = $this->convertUrlQuery($query['query']);
        $queryArr = explode('_', $querys['videoID']);
        return array(
            'userId' => $queryArr[0],
            'videoId' => $queryArr[1]
        );
    }

    private function getCoursewareService()
    {
        return $this->getServiceKernel()->createService('Courseware.CoursewareService');
    }

    private function convertUrlQuery($query)
    {
        $queryParts = explode('&', $query);
        $params = array();
        foreach ($queryParts as $param)
        {
            $item = explode('=', $param);
            $params[$item[0]] = $item[1];
        }
        return $params;
    }

    private function getCategoryService()
    {
        return $this->getServiceKernel()->createService('Taxonomy.CategoryService');
    }
}