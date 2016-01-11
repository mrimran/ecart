<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function solution($K=-4, $L=1, $M=2, $N=6, $P=0, $Q=-1, $R=4, $S=3) {
    // write your code in PHP5.5
    //$K,$L = lower-left, $M,$N = upper-right (1st)
    //$P,$Q = lower-left, $R,$S = upper-right (2nd)
    $rect1 = array("left" => array($K,$L), "right" => array($M,$N));
    $rect2 = array("left" => array($P,$Q), "right" => array($R,$S));
    //area)
    //$area = ($K-$P)*($N-$S)+($L-$Q)*($);
    
    $area = ($rect1['right'][0]-$rect1['left'][0])*($rect1['right'][1]-$rect1['left'][1]) + ($rect2['right'][0]-$rect2['left'][0])*($rect2['right'][1]-$rect2['left'][1]);
    //min/max calculations based on farmula
    $maxKP = max(array($K,$P));
    $minMR = min(array($M,$R));
    
    $maxLQ = max(array($L,$Q));
    $minNS = min(array($N,$S));
    
    $final = $area - ($maxKP-$minMR)*($maxLQ-$minNS);
    
    return ($final >= 2147483647) ? -1 : $final;
}

/*//public int computeArea(int A, int B, int C, int D, int E, int F, int G, int H) {
//public int computeArea(int K, int L, int M, int N, int P, int Q, int R, int S) {
public int computeArea(int A, int B, int C, int D, int E, int F, int G, int H) {
    if(C<E||G<A )
        return (G-E)*(H-F) + (C-A)*(D-B);
 
    if(D<F || H<B)
        return (G-E)*(H-F) + (C-A)*(D-B);
 
    int right = Math.min(C,G);
    int left = Math.max(A,E);
    int top = Math.min(H,D);
    int bottom = Math.max(F,B);
 
    return (G-E)*(H-F) + (C-A)*(D-B) - (right-left)*(top-bottom);
}*/

solution();