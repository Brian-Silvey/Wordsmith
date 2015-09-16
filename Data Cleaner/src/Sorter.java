import java.io.*;
import java.util.*;

public class Sorter {
    public static void main(String[] args) {

        ArrayList<String> data = new ArrayList<>();

        for (char letter = 'a'; letter <= 'z'; letter++) {
            try {
                File fileDir = new File("/Users/BluJay/Sites/Syllable Data/Cleaned Syllable Data/CLEANED_syllable" + letter + ".txt");

                BufferedReader in = new BufferedReader(
                        new InputStreamReader(
                                new FileInputStream(fileDir), "UTF8"));

                String str;

                while ((str = in.readLine()) != null)
                {
                    data.add(str);
                }

                in.close();
            } catch (Exception e) {
                System.out.println(e.getMessage());
            }
        }

        Collections.sort(data);

        try {
            char currLetter = 'a';
            File newFileDir = new File("/Users/BluJay/Sites/Syllable Data/Sorted Syllable Data/SORTED_syllable" + currLetter + ".txt");
            PrintWriter writer = new PrintWriter(newFileDir, "UTF-8");

            for (String word: data)
            {
                if (word.charAt(0) != currLetter)
                {
                    currLetter = word.charAt(0);
                    newFileDir = new File("/Users/BluJay/Sites/Syllable Data/Sorted Syllable Data/SORTED_syllable" + currLetter + ".txt");
                    writer.close();
                    writer = new PrintWriter(newFileDir, "UTF-8");
                }
                writer.println(word);
            }

            writer.close();

        } catch(Exception e) {
            System.out.println(e.getMessage());
        }
    }
}
