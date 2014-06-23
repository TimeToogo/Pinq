<?php

namespace Pinq\Tests\Integration\Collection;

class ApplyTest extends CollectionTest
{
    /**
     * @dataProvider theImplementations
     */
    public function testThatExecutionIsNotDeferred(\Pinq\ICollection $collection, array $data)
    {
        if (count($data) > 0) {
            $this->assertThatExecutionIsNotDeferred([$collection, 'apply']);
        }
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function testThatCollectionApplyOperatesOnTheSameCollection(\Pinq\ICollection $collection, array $data)
    {
        $multiply =
                function (&$i) {
                    $i *= 10;
                };

        $collection->apply($multiply);
        array_walk($data, $multiply);

        $this->assertMatches($collection, $data);
    }

    /**
     * @dataProvider oneToTen
     */
    public function testThatCollectionApplyWorksOnScopedValues(\Pinq\ICollection $collection, array $data)
    {
        $collection
                ->where(function ($i) { return $i % 2 === 0; })
                ->apply(function (&$i) { $i *= 10; });
        
        $this->assertMatches($collection, [
            1,
            20,
            3,
            40,
            5,
            60,
            7,
            80,
            9,
            100
        ]);
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function ttt(\Pinq\ICollection $collection, array $data)
    {
        $collection->removeRange(
                $collection
                ->where(function ($i) { return strlen($i) > 5; }));
        //===
        $collection
                ->where(function ($i) { return strlen($i) > 5; })
                ->clear();
        
        $collection
                ->where(function ($i) { return strlen($i) > 5; })
                ->take(5)
                ->union([1,2,3])
                ->clear();
    }

    /**
     * @dataProvider assocOneToTen
     */
    public function tttLeftJoin(\Pinq\ICollection $collection, array $data)
    {
        //Current
        $collection
                ->groupJoin([/* ... */])
                ->on(function ($user, $post) { return $user['id'] === $post['userId']; })
                ->to(function ($user, \Pinq\ITraversable $posts) {
                    $posts = $posts->isEmpty() ? $posts->append(['title' => null]) : $posts;
                    return $posts
                            ->select(function ($post) use ($user) {
                                return [
                                    'user' => $user['name'],
                                    'post' => $post['title'],
                                ];
                            });
                })
                ->selectMany(function ($joined) {
                    return $joined;
                });
                
        //V1
        $collection
                ->groupJoin([/* ... */])
                ->on(function ($user, $post) { return $user['id'] === $post['userId']; })
                ->to(function ($user, \Pinq\ITraversable $posts) {
                    return $posts
                            ->defaultIfEmpty(['title' => null])
                            ->select(function ($post) use ($user) {
                                return [
                                    'user' => $user['name'],
                                    'post' => $post['title'],
                                ];
                            });
                })
                ->selectMany(function ($joined) {
                    return $joined;
                });
        
        //V2
        $collection
                ->groupJoin([/* ... */])
                ->on(function ($user, $post) { return $user['id'] === $post['userId']; })
                ->toMany(function ($user, \Pinq\ITraversable $posts) {
                    return $posts
                            ->defaultIfEmpty(['title' => null])
                            ->select(function ($post) use ($user) {
                                return [
                                    'user' => $user['name'],
                                    'post' => $post['title'],
                                ];
                            });
                });
        //V3
        $collection
                ->groupJoin([/* ... */])
                ->on(function ($user, $post) { return $user['id'] === $post['userId']; })
                ->withDefault(['title' => null])
                ->toMany(function ($user, \Pinq\ITraversable $posts) {
                    return $posts
                            ->select(function ($post) use ($user) {
                                return [
                                    'user' => $user['name'],
                                    'post' => $post['title'],
                                ];
                            });
                });
        //V4 :)
        $collection
                ->join([/* ... */])
                ->on(function ($user, $post) { return $user['id'] === $post['userId']; })
                ->withDefault(['title' => null])
                ->to(function ($user, $post) {
                    return [
                        'user' => $user['name'],
                        'post' => $post['title'],
                    ];
                });
                
    }
}
