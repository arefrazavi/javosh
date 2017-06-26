#include<Windows.h>
#include<map>
#include<iostream>
#include<fstream> // Stream class to both read and write from/to files.
#include<math.h>
#include<vector>
#include<string>
#include<sstream> 
#include<ctype.h> // tolower function
#include<algorithm>
using namespace std;

class sentence {
	public:
		int time, doc_id, sen_id;
		vector <string> words;
};

class vsm_sentence {
	public:
		sentence *org;
		int score, cluster_id;
		map <int, int> fea_vec; // each pair correponds to word id in lexicon and frequency of its occurance in sentence
		/*Define operator to sort setences based on their scores in descending order*/
		bool operator < (const vsm_sentence &sen) const {
			return score > sen.score;
		}
};

map <string, int> read_stopwords() {
	string file_name = "stopwords.txt";
	ifstream input(file_name);
	map <string, int> stop_words;
	int count = 0;
	if (input.is_open()) {
		string line;
		while (getline(input, line)) {
			istringstream str(line);
			string token;
			str >> token;
			stop_words.insert(pair<string, int>(token, count));
			count++;
		}
		input.close();
		/*for (map<string, int>::iterator it = stop_words.begin(); it != stop_words.end(); it++) {
			cout << it->first << " : " << it->second << endl;
		}*/
	}
	else {
		cout << "Stopwords file not found :(";
	}
	return stop_words;
}

string decapitalize(string word) {
	for (int i = 0; i < word.size(); i++)
		word[i] = tolower(word[i]);
	return word;
}

bool is_stopword(string word, map <string, int> stopwords) {
	//map<string, int>::iterator it;
	return (stopwords.find(word) != stopwords.end());
}

/***Trim word by removing pspecial chararcters ***/ 
string trim_word(string word) {
	string spec_chars[] = { ".", ",", "'", "`", "//", "(", ")"};
	int count = 0, limit = 10;
	int length = sizeof(spec_chars) / sizeof(*spec_chars);
	//int _; cin >> _;
	
	for (int i = 0; i < length; i++) {
		while (word.find(spec_chars[i]) != string::npos && count <= limit) {
			word.erase(word.find(spec_chars[i]), 1);
		}
	}
	return word;

}

/*** (1) Parse Corpus (Read file, build lexicon, store orginal and vsm represtation of corpus)***/
map <string, int> lexicon;
void store_corpus(string path, vector <string> &filenames, vector <vector <sentence> > &org_corpus, vector < vector <vsm_sentence> > &vsm_corpus) {
	map <string, int> stopwords = read_stopwords();
	int files_num = filenames.size(), dic_count = 0;
	//cout << files_num << endl;
	for (int f = 0; f < files_num; f++) {
		//file_name.append(to_string(i));
		//file_name.append(".");
		//file_name
		vector <sentence> org_doc;
		vector <vsm_sentence> vsm_doc;
		org_doc.clear();
		vsm_doc.clear();
		stringstream st;
		st << path << filenames[f];
		string file_name = st.str();
		//cout << file_name << endl;
		ifstream input(file_name);
		if (input.is_open()) {
			string line;
			bool docno_tag = false, text_tag = false, new_sen = true;
			int time, sen_id = 0;
			map <string, int> org_row;
			sentence org_sen;
			map <int, int> vsm_row;
			vsm_sentence vsm_sen;
			while (getline(input, line)) {
				istringstream str(line);
				/*Parse corpus*/
				while (str.good()) {
					string token;
					str >> token;

					/*If <TEXT> tag is observed, content*text) begins*/
					if (token == "<TEXT>") {
						text_tag = true;
						new_sen = true;
						continue;
					}
					/*If </TEXT> tag is observed, content*text) ends and do not continue parsing*/
					else if (token == "</TEXT>") {
						text_tag = false;
						break;
					}
					/*If content(text) has been observed, parse sentences*/
					else if (text_tag) {
						size_t pos = token.find_last_of(".");
						string b_token = token;
						/*If new_sen signal is true, define new orginal and vsm objects*/
						if (new_sen) {
							sentence org_sen;
							vsm_sentence vsm_sen;
							new_sen = false;
						}
						/*Store orginal word in orginal object*/
						org_sen.words.push_back(token);
						//org_count++;
						/*Store lower-case non-stop word stem in both lexicon (if it's new) and vsm object*/
						token = trim_word(token);
						token = decapitalize(token);
						if (!is_stopword(token, stopwords) && token != "") {
							char word[100];
							strncpy_s(word, token.c_str(), sizeof(word));
							word[sizeof(word)-1] = 0;
							char * stemming(char *);
							char *a = stemming(word);
							token = string(word);
							int word_id, word_count;
							map<string, int>::iterator it = lexicon.find(token);
							if (it == lexicon.end()) {
								lexicon.insert(pair<string, int>(token, dic_count));
								word_id = dic_count;
								dic_count++;
							}
							else {
								word_id = it->second;
							}
							map<int, int>::iterator it1 = vsm_sen.fea_vec.find(word_id);
							if (it1 == vsm_sen.fea_vec.end()) {
								word_count = 1;
								vsm_sen.fea_vec.insert(pair<int, int>(word_id, word_count));
							}
							else {
								vsm_sen.fea_vec[word_id]++;
							}


						}
						/*If last charater of the token(word) before timming is . then it's the last word of the object(sentence) and sentence is complete*/
						if (pos != string::npos) {
							if (b_token.substr(pos + 1) == "" || b_token.substr(pos + 1) == "''") {
								org_sen.time = time;
								org_sen.doc_id = f;
								org_sen.sen_id = sen_id; 
								org_doc.push_back(org_sen);
								vsm_doc.push_back(vsm_sen);
								org_sen = {};
								vsm_sen = {};
								sen_id++;
								new_sen = true;
							}
						}
					}
					/*If <DOCNO> tag is observed, start collecting document info in the next round*/
					else if (token == "<DOCNO>") {
						docno_tag = true;
						//continue;
					}
					else if (token == "</DOCNO>") {
						docno_tag = false;
						//continue;
					}
					/*If <DOCNO> tag has been observed, store document info*/
					else if (docno_tag) {
						size_t last = token.find_last_of(".");
						//doc_id = stoi(token.substr(last+1));
						size_t first = token.find("W");
						time = stoi(token.substr(first + 1, last - first - 1));
					} 
					else {
						//cout << token;
						//int _; cin >> _;
						break;
					}
				} //end 
			} //end while(getting line)
			input.close();
		} // if(opening file)
		else {
			cout << "file not found!" << endl;
		}
		org_corpus.push_back(org_doc);
		vsm_corpus.push_back(vsm_doc);
		org_doc.clear();
		vsm_doc.clear();

	}
	//cout << vsm_corpus.size() << " " << org_corpus.size() << endl;
	for (int i = 0; i < vsm_corpus.size(); i++) {
		for (int j = 0; j < vsm_corpus[i].size(); j++) {
			vsm_corpus[i][j].org = &org_corpus[i][j];
		}
	}

	/*for (int i = 0; i < vsm_corpus.size(); i++) {
		for (int j = 0; j < vsm_corpus[i].size(); j++) {
			for (map<int, int>::iterator it1 = vsm_corpus[i][j].fea_vec.begin(); it1 != vsm_corpus[i][j].fea_vec.end(); it1++) {
				for (map<string, int>::iterator it2 = lexicon.begin(); it2 != lexicon.end(); it2++) {
					if (it2->second == it1->first) {
						cout << it2->first << " (" << it1->second << ")";
						break;
					}
				}
			}
			cout << endl;
		}
	}*/

}

/***(2) Query Processing***/
vector <int> process_query(string keyword_line) {
	//cout << "**" << keyword_line << endl;
	vector <int> keywords;
	istringstream str(keyword_line);
	map <string, int> stopwords = read_stopwords();
	while (str.good()) {
		string token;
		str >> token;
		token = trim_word(token);
		if (!is_stopword(token, stopwords) && token != "") {
			char word[100];
			strncpy_s(word, token.c_str(), sizeof(word));
			word[sizeof(word)-1] = 0;
			char * stemming(char *);
			char *a = stemming(word);
			token = string(word);
			map<string, int>::iterator it = lexicon.find(token);
			if (it != lexicon.end()) {
				keywords.push_back(it->second);
			}
		}
	}
	for (int i = 0; i < keywords.size(); i++){
	//	cout << "****" << keywords[i] << endl;
	}
	return keywords;
}

/***(3) Content Selection***/
vector <vsm_sentence> select_content(vector <int> &keywords, int max_len, vector < vector <vsm_sentence> > &vsm_corpus) {
	int score = 0;
	for (int i = 0; i < vsm_corpus.size(); i++) {
		for (int j = 0; j < vsm_corpus[i].size(); j++) {
			score = 0;
			for (int k = 0; k < keywords.size(); k++) {
				map<int, int>::iterator it = vsm_corpus[i][j].fea_vec.find(keywords[k]);
				if (it != vsm_corpus[i][j].fea_vec.end()) {
					score += it->second;
				}
			}
			//cout << "**" << score << endl;
			vsm_corpus[i][j].score = score;
		}
	}
	//cout << "**********************" << vsm_corpus.size() << endl;
	//int _; cin >> _;
	vector <vsm_sentence> ordered_corpus;
	vsm_sentence tmp_sen;
	/*Sort sentences based on score in descending order (Insertion Sort)*/
	for (int i = 0; i < vsm_corpus.size(); i++) {
		for (int j = 0; j < vsm_corpus[i].size(); j++) {
			ordered_corpus.push_back(vsm_corpus[i][j]);
			int k = ordered_corpus.size() - 1;
			while (k > 0) {
				if (ordered_corpus[k - 1].score < ordered_corpus[k].score) {
					tmp_sen = ordered_corpus[k - 1];
					ordered_corpus[k-1] = ordered_corpus[k];
					ordered_corpus[k] = tmp_sen;
					k--;
				}
				else {
					break;
				}
			}
		}
	}
	//cout << "*********" << ordered_corpus.size() << endl;
	//sort(vsm_corpus.begin(), vsm_corpus.end());
	vector <vsm_sentence> sel_sentences;
	int i = 0 , k = 0;
	while (k < max_len && i < ordered_corpus.size()) {
		if (ordered_corpus[i].score > 0) {
			sel_sentences.push_back(ordered_corpus[i]);
			k++;
		}
		i++;
	}
	//cout << "** " << sel_sentences.size() << endl;
	/*for (i = 0; i < sel_sentences.size(); i++) {
		cout << sel_sentences[i].org->sen_id << " : " << sel_sentences[i].score << endl;
	}*/
	return sel_sentences;
}

int coh_chr(vsm_sentence &u, vsm_sentence &v) {
	int coh_chr = -1;
	if (u.org->time < v.org->time
		|| (u.org->doc_id == v.org->doc_id && u.org->sen_id < v.org->sen_id)
		|| (u.org->doc_id == v.org->doc_id && u.org->time < v.org->time))
		coh_chr = 1;
	return coh_chr;
}

double probability(int next, int before, vector < vector <vsm_sentence> > &vsm_corpus) {
	int count_both = 0, count_before = 0;
	for (int i = 0; i < vsm_corpus.size(); i++) {
		for (int j = 0; j < vsm_corpus[i].size() - 1; j++) {
			map<int, int>::iterator c1 = vsm_corpus[i][j].fea_vec.find(before);
			if (c1 != vsm_corpus[i][j].fea_vec.end()) {
				count_before += c1->second;
				map<int, int>::iterator c2 = vsm_corpus[i][j + 1].fea_vec.find(next);
				if (c2 != vsm_corpus[i][j+1].fea_vec.end()) {
					count_both += c1->second * c2->second;
				}
			}
		}
	}
	double prob = (count_both / count_before);
	return prob;
}

int coh_trans(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	double transprob_uv = 0;
	int denom = u.fea_vec.size() * v.fea_vec.size();
	for (map<int, int>::iterator it1 = u.fea_vec.begin(); it1 != u.fea_vec.end(); it1++) {
		for (map<int, int>::iterator it2 = v.fea_vec.begin(); it2 != v.fea_vec.end(); it2++) {
			transprob_uv += probability(it1->first, it2->first, vsm_corpus);
		}
	}
	
	transprob_uv = double(transprob_uv / denom);
	double transprob_vu = 0;
	for (map<int, int>::iterator it3 = v.fea_vec.begin(); it3 != v.fea_vec.end(); it3++) {
		for (map<int, int>::iterator it4 = u.fea_vec.begin(); it4 != u.fea_vec.end(); it4++) {
			transprob_vu += probability(it3->first, it4->first, vsm_corpus);
		}
	}
	transprob_vu = double(transprob_vu / denom);

	int coh_trans = (transprob_uv <= transprob_vu) ? 1 : -1;
	
	return coh_trans;
}

double pre(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	double numerator, denom_i, denom_v = 0;
	double max_sim = 0;
	for (map<int, int>::iterator it2 = v.fea_vec.begin(); it2 != v.fea_vec.end(); it2++) {
		denom_v += pow((it2->second), 2);
	}
	int doc_id = u.org->doc_id;
	for (int j = u.org->sen_id - 1; j >= 0; j--) {
		numerator = 0;
		denom_i = 0;
		for (map<int, int>::iterator it = vsm_corpus[doc_id][j].fea_vec.begin(); it != vsm_corpus[doc_id][j].fea_vec.end(); it++) {
			denom_i += pow((it->second), 2);
			map<int, int>::iterator it1 = v.fea_vec.find(it->first);
			if (it1 != v.fea_vec.end()) {
				numerator += it->second * it1->second;
				
			}
		}
		double cos_sim = double((numerator) / (denom_i*denom_v));
		if (cos_sim > max_sim) {
			max_sim = cos_sim;
			//cout << "max_sim: " << max_sim << endl;
		}

	}
	return max_sim;
}

int coh_pre(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	if (pre(u, v, vsm_corpus) >= pre(v, u, vsm_corpus)) {
		return 1;
	}
	return -1;
}

double suc(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	double numerator, denom_i, denom_v = 0;
	double max_sim = 0;
	for (map<int, int>::iterator it2 = v.fea_vec.begin(); it2 != v.fea_vec.end(); it2++) {
		denom_v += pow((it2->second), 2);
	}
	int doc_id = u.org->doc_id;
	int end = vsm_corpus[doc_id].size() - 1;
	for (int j = u.org->sen_id + 1; j <= end; j++) {
		numerator = 0;
		denom_i = 0;
		for (map<int, int>::iterator it = vsm_corpus[doc_id][j].fea_vec.begin(); it != vsm_corpus[doc_id][j].fea_vec.end(); it++) {
			denom_i += pow((it->second), 2);
			map<int, int>::iterator it1 = v.fea_vec.find(it->first);
			if (it1 != v.fea_vec.end()) {
				numerator += it->second * it1->second;
			}
		}
		double cos_sim = double((numerator) / (denom_i*denom_v));
		if (cos_sim > max_sim) {
			max_sim = cos_sim;
		}

	}
	return max_sim;
}

int coh_suc(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	if (suc(u, v, vsm_corpus) <= suc(v, u, vsm_corpus)) {
		return 1;
	}
	return -1;
}

double coh_total(vsm_sentence &u, vsm_sentence &v, vector < vector <vsm_sentence> > &vsm_corpus) {
	double coh_total;
	double w[] = { 0.25, 0.25, 0.25, 0.25 };
	coh_total = w[0] * coh_chr(u, v) + w[1] * coh_trans(u, v, vsm_corpus)
				//+ w[2] * coh_pre(u, v, vsm_corpus) + w[3] * coh_suc(u, v, vsm_corpus)
				;

	return coh_total;
}

void io_merge(vector <vsm_sentence> &S, int b, int m, int e, vector < vector <vsm_sentence> > &vsm_corpus) {
	vector <vsm_sentence> O;
	if (b < e) {
		int i = b, j = m + 1;
		while (i <= m && j <= e) {
			//cout << "** " << S[i].fea_vec.size() << ": ";
			if (coh_total(S[i], S[j], vsm_corpus) >= 0) {
			//	cout << "coh_total: " << coh_total(S[i], S[j], vsm_corpus) << endl;
				O.push_back(S[i]);
				i++;
			}
			else {
				O.push_back(S[j]);
				j++;
			}
			//cout << i << " : " << j << endl;
		}
		//cout << O.size() << endl;
		while (i <= m) {
			O.push_back(S[i]);
			i++;
		}
		while (j <= e) {
			O.push_back(S[j]);
			j++;
		}
		i = b; j = 0;
		//cout << O.size() << " :" << e - b + 1 << endl;
		while (i <= e) {
			S[i] = O[j];
			i++;
			j++;
		}
	}
}

/***(4) Information Ordering***/
void mergesort_sentence(vector <vsm_sentence> &sel_sentences, int b, int e, vector < vector <vsm_sentence> > &vsm_corpus) {
	if (b < e) {
		int m = int(round((b + e) / 2));
		mergesort_sentence(sel_sentences, b, m, vsm_corpus);
		mergesort_sentence(sel_sentences, m + 1, e, vsm_corpus);
		io_merge(sel_sentences, b, m, e, vsm_corpus);
	}
}

void topicbased_cluster_sentence(vector <vsm_sentence> &sel_sentences, double alpha) {
	vector <map <int, int> > cluster_vecs;
	sel_sentences[0].cluster_id = 0;
	double numerator, denom_c, denom_s;
	int cluster_id = 0;
	map<int, int> test_vec, new_vec;
	double d_min = alpha, dist;

	cluster_vecs.push_back(sel_sentences[0].fea_vec);
	for (int i = 1; i < sel_sentences.size(); i++) {
		denom_s = 0;
		new_vec.clear();
		for (map<int, int>::iterator it0 = sel_sentences[i].fea_vec.begin(); it0 != sel_sentences[i].fea_vec.end(); it0++) {
			denom_s += pow((it0->second), 2);
		}
		int clus_num = cluster_vecs.size();
		for (int j = 0; j < clus_num; j++) {
			numerator = 0;
			denom_c = 0;
			test_vec.clear();
			for (map<int, int>::iterator it = cluster_vecs[j].begin(); it != cluster_vecs[j].end(); it++) {
				denom_c += pow((it->second), 2);
				map<int, int>::iterator it1 = sel_sentences[i].fea_vec.find(it->first);
				if (it1 != sel_sentences[i].fea_vec.end()) {
					numerator += it->second * it1->second;
					int intersection = (it->second < it1->second) ? it->second : it1->second;
					test_vec.insert(pair<int, int>(it1->first, intersection));
				}
			}
			dist = double(1-((numerator) / (denom_s * denom_c)));
			if (dist < d_min) {
				cout << dist << endl;
				cluster_id = j;
				d_min = dist;
				new_vec = test_vec;
				//cout << "**" << d_min << endl;
			}
		}
		//int _; cin >> _;
		if (!new_vec.empty()) {
			sel_sentences[i].cluster_id = cluster_id;
			cluster_vecs[cluster_id] = new_vec;
			new_vec.clear();
		}
		else {
			sel_sentences[i].cluster_id = clus_num;
			cluster_vecs.push_back(sel_sentences[i].fea_vec);
		}

	}
	vector <vsm_sentence> ordered_sentences;
	
	for (int i = 0; i < sel_sentences.size(); i++) {
		ordered_sentences.push_back(sel_sentences[i]);
		int k = ordered_sentences.size() - 1;
		while (k > 0) {
			if (ordered_sentences[k - 1].cluster_id > ordered_sentences[k].cluster_id) {
				vsm_sentence tmp_sen = ordered_sentences[k - 1];
				ordered_sentences[k - 1] = ordered_sentences[k];
				ordered_sentences[k] = tmp_sen;
				k--;
			}
			else {
				break;
			}
		}
	}
	sel_sentences = ordered_sentences;
}


int main() {
	string path = "corpus\\duc2004\\d30001t\\";
	vector<string> filenames{ "APW19981016.0240", "APW19981022.0269", "APW19981026.0220", 
							"APW19981027.0491", "APW19981031.0167","APW19981113.0251", 
							"APW19981116.0205", "APW19981118.0276", "APW19981120.0274", "APW19981124.0267"};
	vector <vector <sentence> > org_corpus; 
	vector <vector <vsm_sentence> > vsm_corpus;
	store_corpus(path, filenames, org_corpus, vsm_corpus);
	string keyword_line;
	cout << "Seach Keywords: ";
	getline(cin, keyword_line);
	//eyword_line = "Hun sen opposition";
	vector <int> keywords = process_query(keyword_line);
	if (!keywords.size()) {
		cout << "No summary found";
		return 0;
	}
	
	int max_len, corpus_size = 0;
	for (int i = 0; i < vsm_corpus.size(); i++) {
		corpus_size += vsm_corpus[i].size();
	}
	max_len = corpus_size;
	cout << "Enter maximum length of summary: ";
	cin >> max_len;
	if (max_len > corpus_size) {
		cout << "Summary length exceeds corpus length!" << endl;
		return 0;
	}
	//max_len = 5;
	vector <vsm_sentence> sel_sentences = select_content(keywords, max_len, vsm_corpus);
	
	cout << "*Summary Before Ordering*" << endl;
	for (int i = 0; i < sel_sentences.size(); i++) {
		vector <string> org_sen = sel_sentences[i].org->words;
		cout << "(" << i + 1 << ")  ";
		for (int j = 0; j < org_sen.size(); j++) {
			cout << org_sen[j] << " ";
		}
		cout << endl;
	}
	
	mergesort_sentence(sel_sentences, 0, sel_sentences.size()-1, vsm_corpus);
	
	cout << endl << "*Summary by Merge-sorte Ordering*" << endl;
	for (int i = 0; i < sel_sentences.size(); i++) {
		vector <string> org_sen = sel_sentences[i].org->words;
		cout << "(" << i+1 << ")  ";
		for (int j = 0; j < org_sen.size(); j++) {
			cout << org_sen[j] << " ";
		}
		cout << endl;
	}
	cout << "Enter alpha parameter for topic-based clustering: ";
	double alpha;
	cin >> alpha;
	topicbased_cluster_sentence(sel_sentences, alpha);

	cout <<  endl << "*Summary By Topic-based Clustering*" << endl;
	for (int i = 0; i < sel_sentences.size(); i++) {
		vector <string> org_sen = sel_sentences[i].org->words;
		cout << "(" << i + 1 << ")  ";
		for (int j = 0; j < org_sen.size(); j++) {
			cout << org_sen[j] << " ";
		}
		cout << endl;
	}
	
}