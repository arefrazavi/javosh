// Created by arefr on 6/22/2018.
//

#ifndef COMPUTATION_WORD_H
#define COMPUTATION_WORD_H

#include <string>     // std::wstring, std::to_wstring

using namespace std;

class Word {
public:
    int id;
    int category_id;
    wstring value;
    int count;
    string occurrences;
    string entropy;
    char sentiment_polarity;
};


#endif //COMPUTATION_WORD_H
