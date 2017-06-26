#include<map>
#include<iostream>
#include<fstream> // Stream class to both read and write from/to files.
#include<math.h>
#include<vector>
#include<string>
#include<sstream>
#include<ctype.h> // tolower function
#include<algorithm>
#include <typeinfo>
#include <string>     // std::wstring, std::to_wstring
#include <wchar.h>
#include <stddef.h>
#include <locale.h>

using namespace std;

int search(wstring letter, vector <wstring> letters)
{
    for(int i=0; i < letters.size(); i++) {
        if (letter == letters[i]) {
            return true;
        }
    }
    return false;
}

class Sentence {
public:

    //find suffix
    int FDA[8][8] = {
            {12, 12, 12, 2, 1, 12, 12, 12},
            {3, 12, 12, 12, 12, 12, 12, 12},
            {12, 12, 12, 2, 1, 12, 4, 12},
            {8, 5, 8, 8, 8, 8, 8, 8},
            {6, 10, 10, 10, 10, 10, 10, 10},
            {9, 9, 12, 9, 9, 9, 9, 9},
            {10, 10, 10, 10, 10, 7, 10, 10},
            {11, 11, 11, 11, 11, 11, 11, 11},
    };

    vector <wchar_t> letters = {L'ا', L'ت', L'س', L'م', L'ن', L'ه', L'ی'};

    string suffixGroup[13] = {"NIL", "NIL", "NIL", "NIL", "NIL", "PL2", "VB2", "VB2", "PL2", "PO3", "VB2", "VB4", "NIL"};


    wstring extractStem(wstring word)
    {
        wstring stem = word;
        string suffixType = determineSuffix(word);

        if (suffixType == "PL2") {
            word.erase(word.end() - 2, word.end());
            stem = this->extractStem(word);
        } else if (suffixType == "PO3") {
            word.erase(word.end() - 3, word.end());
            stem = this->extractStem(word);
        } else if (suffixType == "VB2") {
                word.erase(word.end() - 2, word.end());
            stem = this->removePrefix(word);
        } else if (suffixType == "VB4") {
            word.erase(word.end() - 4, word.end());
            stem = this->removePrefix(word);
        }
        return stem;
    }

    string determineSuffix(wstring word)
    {
        int wordLastIndex = word.length()-1;
        wstring stem = word;
        int currentState = 0;
        int transitionIndex; //Column Index in FDA
        vector <wchar_t>::iterator letterBeginIterator = letters.begin();
        vector <wchar_t>::iterator letterEndIterator = letters.end();

        for(int i = wordLastIndex; i >= 3; i--) {
            vector <wchar_t>::iterator it = find(letterBeginIterator, letterEndIterator, word[i]);
            if( it != letterEndIterator) {
                transitionIndex = distance(letterBeginIterator, it);
            } else {
                transitionIndex = 7;
            }
            currentState = FDA[currentState][transitionIndex];
        }

        string suffixType = suffixGroup[currentState];

        return suffixType;
    }

    wstring removePrefix(wstring word)
    {
        return word;
    }

};


int main() {
    setlocale(LC_ALL, "en_US.UTF-8");
    locale::global(std::locale("en_US.utf8"));
    cout.imbue(std::locale());

    //std::cout << "Hello, fff!" << std::endl;

    Sentence sentence;
    //wchar_t word[] = L"سلام";
    //string temp;
    //utf8::replace_invalid(word.begin(), word.end(), back_inserter(temp));
    wstring word = L"دیدم";
    wstring stem = sentence.extractStem(word);

    wcout << "Stem: " << stem << endl;
    return 0;
}