import java.io.*;
import java.util.*;

public class Cleaner {
    public static void main(String[] args) {

        for (char letter = 'a'; letter <= 'z'; letter++) {
            try {
                File fileDir = new File("/Users/BluJay/Sites/syllable" + letter + ".txt");
                File newFileDir = new File("/Users/BluJay/Sites/CLEANED_syllable" + letter + ".txt");
                PrintWriter writer = new PrintWriter(newFileDir, "UTF-8");

                BufferedReader in = new BufferedReader(
                        new InputStreamReader(
                                new FileInputStream(fileDir), "UTF8"));

                String str;
                String key;
                String value;

                Map<String, String> words = new LinkedHashMap<>();

                while ((str = in.readLine()) != null)
                {
                    str = str.toLowerCase();
                    key = str.substring(0, str.indexOf(':'));
                    value = str.substring(str.indexOf(':') + 1, str.length());

                    for (int x = 0; x < key.length(); x++)
                    {
                        if (key.charAt(x) < 97 || key.charAt(x) > 122)
                        {
                            break;
                        }
                        if (x == key.length() - 1)
                        {
                            words.put(key, value);
                        }
                    }
                }

                Iterator it = words.entrySet().iterator();
                while (it.hasNext()) {
                    Map.Entry pair = (Map.Entry) it.next();
                    writer.println(pair.getKey() + ":" + pair.getValue());
                    it.remove(); // avoids a ConcurrentModificationException
                }

                in.close();
                writer.close();
            } catch (Exception e) {
                System.out.println(e.getMessage());
            }
        }
    }
}
